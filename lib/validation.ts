import { z } from "zod";

export const businessTypes = [
  "Logistics",
  "Real Estate",
  "Law Firm",
  "Professional / Consulting Services",
  "Other Service Business",
] as const;

export const teamSizes = ["1-10", "11-50", "51-200", "200+"] as const;

export const contactFormSchema = z.object({
  name: z.string().trim().min(2, "Please enter your name.").max(120),
  email: z.string().trim().email("Please enter a valid work email."),
  company: z.string().trim().min(2, "Please enter your company name.").max(160),
  website: z.string().trim().max(200).optional().or(z.literal("")),
  businessType: z.enum(businessTypes, {
    error: "Please select a business type.",
  }),
  automationGoal: z
    .string()
    .trim()
    .min(10, "Please tell us a little about what you'd like to automate.")
    .max(2000),
  teamSize: z.enum(teamSizes, { error: "Please select a team size." }),
  consent: z.literal(true, {
    error: "Please confirm you're happy for us to contact you.",
  }),
  // Honeypot field: real users never fill this in.
  companyWebsiteUrl: z.string().max(0, "Spam detected.").optional().or(z.literal("")),
});

export type ContactFormValues = z.infer<typeof contactFormSchema>;
