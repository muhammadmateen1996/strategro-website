import { NextResponse } from "next/server";
import { contactFormSchema } from "@/lib/validation";
import { isLeadWebhookConfigured, sendLeadToN8n } from "@/lib/n8n";

export async function POST(request: Request) {
  let body: unknown;
  try {
    body = await request.json();
  } catch {
    return NextResponse.json({ ok: false, error: "invalid_json" }, { status: 400 });
  }

  const parsed = contactFormSchema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(
      { ok: false, error: "validation_failed", issues: parsed.error.issues },
      { status: 422 }
    );
  }

  // Honeypot: if this hidden field is filled, silently report success to the bot
  // without sending anything, so it doesn't learn to avoid the trap.
  if (parsed.data.companyWebsiteUrl) {
    return NextResponse.json({ ok: true });
  }

  if (!isLeadWebhookConfigured()) {
    return NextResponse.json({ ok: false, error: "webhook_not_configured" }, { status: 503 });
  }

  const lead = {
    name: parsed.data.name,
    email: parsed.data.email,
    company: parsed.data.company,
    website: parsed.data.website,
    businessType: parsed.data.businessType,
    automationGoal: parsed.data.automationGoal,
    teamSize: parsed.data.teamSize,
    consent: parsed.data.consent,
  };

  const result = await sendLeadToN8n(lead, {
    submittedAt: new Date().toISOString(),
    source: "website_contact_form",
  });

  if (!result.ok) {
    return NextResponse.json({ ok: false, error: result.error ?? "send_failed" }, { status: 502 });
  }

  return NextResponse.json({ ok: true });
}
