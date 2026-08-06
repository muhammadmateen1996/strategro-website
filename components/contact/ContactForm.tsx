"use client";

import { useId, useState, type FormEvent } from "react";
import { Loader2, CheckCircle2, AlertCircle } from "lucide-react";
import { businessTypes, teamSizes } from "@/lib/validation";

type Status = "idle" | "submitting" | "success" | "error";

const fieldClasses =
  "focus-ring w-full rounded-xl border border-ink-950/15 bg-white px-4 py-3 text-sm text-ink-950 placeholder:text-ink-700/40 transition-colors focus:border-gold-500";
const labelClasses = "text-sm font-medium text-ink-950";

export function ContactForm() {
  const [status, setStatus] = useState<Status>("idle");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const formId = useId();

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("submitting");
    setErrorMessage(null);

    const form = event.currentTarget;
    const formData = new FormData(form);

    const payload = {
      name: formData.get("name"),
      email: formData.get("email"),
      company: formData.get("company"),
      website: formData.get("website") ?? "",
      businessType: formData.get("businessType"),
      automationGoal: formData.get("automationGoal"),
      teamSize: formData.get("teamSize"),
      consent: formData.get("consent") === "on",
      companyWebsiteUrl: formData.get("companyWebsiteUrl") ?? "",
    };

    try {
      const response = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (!response.ok || !data.ok) {
        setStatus("error");
        setErrorMessage(
          data.error === "validation_failed"
            ? "Please check the highlighted fields and try again."
            : "Something went wrong sending your enquiry. Please try again or email us directly."
        );
        return;
      }

      setStatus("success");
      form.reset();
    } catch {
      setStatus("error");
      setErrorMessage("Something went wrong sending your enquiry. Please try again or email us directly.");
    }
  }

  if (status === "success") {
    return (
      <div className="flex flex-col items-center rounded-2xl border border-gold-500/30 bg-ink-950 px-8 py-14 text-center">
        <CheckCircle2 className="size-10 text-gold-400" aria-hidden="true" />
        <h3 className="mt-5 font-display text-2xl text-paper-50">Thanks &mdash; that&rsquo;s sent.</h3>
        <p className="mt-2 max-w-sm text-sm leading-relaxed text-paper-100/70">
          We&rsquo;ll review your enquiry and get back to you within one business day to arrange
          your AI Systems Audit.
        </p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {/* Honeypot field: hidden from real users, catches simple bots. */}
      <div className="hidden" aria-hidden="true">
        <label htmlFor={`${formId}-hp`}>Company website</label>
        <input
          id={`${formId}-hp`}
          name="companyWebsiteUrl"
          type="text"
          tabIndex={-1}
          autoComplete="off"
        />
      </div>

      <div className="grid gap-6 sm:grid-cols-2">
        <div>
          <label htmlFor={`${formId}-name`} className={labelClasses}>
            Name
          </label>
          <input id={`${formId}-name`} name="name" type="text" required className={`mt-2 ${fieldClasses}`} />
        </div>
        <div>
          <label htmlFor={`${formId}-email`} className={labelClasses}>
            Work email
          </label>
          <input
            id={`${formId}-email`}
            name="email"
            type="email"
            required
            className={`mt-2 ${fieldClasses}`}
          />
        </div>
      </div>

      <div className="grid gap-6 sm:grid-cols-2">
        <div>
          <label htmlFor={`${formId}-company`} className={labelClasses}>
            Company
          </label>
          <input
            id={`${formId}-company`}
            name="company"
            type="text"
            required
            className={`mt-2 ${fieldClasses}`}
          />
        </div>
        <div>
          <label htmlFor={`${formId}-website`} className={labelClasses}>
            Website or LinkedIn URL
          </label>
          <input id={`${formId}-website`} name="website" type="text" className={`mt-2 ${fieldClasses}`} />
        </div>
      </div>

      <div className="grid gap-6 sm:grid-cols-2">
        <div>
          <label htmlFor={`${formId}-businessType`} className={labelClasses}>
            Business type
          </label>
          <select
            id={`${formId}-businessType`}
            name="businessType"
            required
            defaultValue=""
            className={`mt-2 ${fieldClasses}`}
          >
            <option value="" disabled>
              Select an option
            </option>
            {businessTypes.map((type) => (
              <option key={type} value={type}>
                {type}
              </option>
            ))}
          </select>
        </div>
        <div>
          <label htmlFor={`${formId}-teamSize`} className={labelClasses}>
            Approximate team size
          </label>
          <select
            id={`${formId}-teamSize`}
            name="teamSize"
            required
            defaultValue=""
            className={`mt-2 ${fieldClasses}`}
          >
            <option value="" disabled>
              Select an option
            </option>
            {teamSizes.map((size) => (
              <option key={size} value={size}>
                {size}
              </option>
            ))}
          </select>
        </div>
      </div>

      <div>
        <label htmlFor={`${formId}-automationGoal`} className={labelClasses}>
          What would you like to automate?
        </label>
        <textarea
          id={`${formId}-automationGoal`}
          name="automationGoal"
          required
          rows={4}
          className={`mt-2 ${fieldClasses}`}
        />
      </div>

      <div className="flex items-start gap-3">
        <input
          id={`${formId}-consent`}
          name="consent"
          type="checkbox"
          required
          className="focus-ring mt-1 size-4 rounded border-ink-950/30 text-gold-500"
        />
        <label htmlFor={`${formId}-consent`} className="text-sm text-ink-700">
          I&rsquo;m happy for Strategro to contact me about this enquiry.
        </label>
      </div>

      {status === "error" && errorMessage && (
        <div className="flex items-start gap-2.5 rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800">
          <AlertCircle className="mt-0.5 size-4 shrink-0" aria-hidden="true" />
          {errorMessage}
        </div>
      )}

      <button
        type="submit"
        disabled={status === "submitting"}
        className="focus-ring inline-flex w-full items-center justify-center gap-2 rounded-full bg-gold-500 px-6 py-3.5 text-sm font-semibold text-ink-950 transition-colors hover:bg-gold-400 disabled:opacity-60 sm:w-auto"
      >
        {status === "submitting" && <Loader2 className="size-4 animate-spin" aria-hidden="true" />}
        {status === "submitting" ? "Sending..." : "Book an AI Systems Audit"}
      </button>
    </form>
  );
}
