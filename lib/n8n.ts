import "server-only";
import crypto from "node:crypto";
import type { ContactFormValues } from "@/lib/validation";

const WEBHOOK_URL = process.env.N8N_LEAD_WEBHOOK_URL;
const WEBHOOK_SECRET = process.env.N8N_LEAD_WEBHOOK_SECRET;

export function isLeadWebhookConfigured(): boolean {
  return Boolean(WEBHOOK_URL);
}

function signPayload(payload: string): string {
  return crypto.createHmac("sha256", WEBHOOK_SECRET ?? "").update(payload).digest("hex");
}

export async function sendLeadToN8n(
  lead: Omit<ContactFormValues, "companyWebsiteUrl">,
  meta: { submittedAt: string; source: string }
): Promise<{ ok: boolean; error?: string }> {
  if (!WEBHOOK_URL) {
    return { ok: false, error: "not_configured" };
  }

  const body = JSON.stringify({ ...lead, ...meta });
  const headers: Record<string, string> = { "Content-Type": "application/json" };

  if (WEBHOOK_SECRET) {
    headers["X-Strategro-Signature"] = signPayload(body);
  }

  try {
    const response = await fetch(WEBHOOK_URL, {
      method: "POST",
      headers,
      body,
      signal: AbortSignal.timeout(8000),
    });

    if (!response.ok) {
      return { ok: false, error: `webhook_status_${response.status}` };
    }

    return { ok: true };
  } catch (error) {
    console.error("Failed to send lead to n8n webhook:", error);
    return { ok: false, error: "network_error" };
  }
}
