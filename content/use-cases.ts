import type { UseCaseStory } from "@/types/content";

/**
 * A mix of real, delivered Strategro projects (isReal: true, with results
 * and — where the client has agreed to be named — a client name) and
 * illustrative examples of the type of system Strategro builds where no
 * real client backs the specific scenario yet. Each usage site must
 * disclose which is which.
 */
export const useCases: UseCaseStory[] = [
  {
    slug: "peerz-freight-quote-calculator",
    industry: "Logistics",
    title: "Real-time freight quotes, replacing a 5–10 minute manual process",
    client: "Peerz Ltd",
    isReal: true,
    scenario:
      "Peerz Ltd, a UK logistics business, needed customers to get an accurate freight quote instantly rather than waiting on a team member to manually calculate distance, vehicle type, service level, and surcharges.",
    before: [
      "Every quote took 5–10 minutes of manual calculation",
      "Pricing depended on whoever was available to work it out",
      "Manual calculation left room for pricing errors",
    ],
    after: [
      "Quotes generate in real time from a web form, with no manual step",
      "Pricing accounts for distance, vehicle type, service level, pallet count, weight, and surcharges automatically",
      "Leads and quotes are captured straight into the CRM with instant email delivery",
    ],
    systemFlow: [
      "Customer submits a quote request via web form",
      "Google Maps Distance Matrix API calculates route distance",
      "Custom pricing logic applies vehicle type, service level, and surcharges",
      "Instant quote delivered by email, lead logged automatically",
    ],
    results: [
      "Quote time cut from 5–10 minutes to real time (seconds) — a 95% reduction",
      "100% elimination of manual calculation errors",
      "24/7 quote availability, no longer dependent on staff availability",
    ],
  },
  {
    slug: "real-estate-lead-document-automation",
    industry: "Real Estate",
    title: "From enquiry to signed documents, without manual admin",
    isReal: true,
    scenario:
      "A real estate workflow where every new lead used to trigger 30–60 minutes of manual admin: creating a CRM record, setting up folders, sending upload links, and scheduling reminders by hand.",
    before: [
      "Each new deal took 30–60 minutes of manual setup",
      "Document collection dragged on for 2–4 weeks",
      "Follow-ups were easy to forget without a manual tracking system",
    ],
    after: [
      "Every step from lead capture to document collection runs automatically",
      "CRM entries, folder structures, and secure upload links are created the moment a lead comes in",
      "Reminder emails go out on schedule, to both client and agent",
    ],
    systemFlow: [
      "Lead submitted via web form",
      "CRM entry, Drive folder structure, and secure upload link created automatically",
      "Client and agent notified with instructions",
      "Scheduled reminders sent until documents are received",
    ],
    results: [
      "Admin time per deal cut from 30–60 minutes to around 5 minutes — an 85–90% reduction",
      "Document collection turnaround roughly halved, from 2–4 weeks to 1–2 weeks",
      "No more missed follow-ups or manual data entry errors",
    ],
  },
  {
    slug: "law-firm-intake",
    industry: "Law Firm",
    title: "New client intake, qualified before it reaches a fee earner",
    isReal: false,
    scenario:
      "A prospective client submits an enquiry through the website outside office hours, describing a commercial dispute.",
    before: [
      "Enquiry sits in a shared inbox until someone checks it the next morning",
      "Fee earner spends time on an initial call gathering basic facts",
      "No consistent record of urgency or case type across enquiries",
    ],
    after: [
      "Chatbot captures the enquiry and asks structured qualifying questions",
      "Case type and urgency are logged automatically against the firm's criteria",
      "A summary is routed to the right fee earner, ready before any call happens",
    ],
    systemFlow: [
      "Website enquiry captured",
      "AI chatbot qualifies case type and urgency",
      "Summary generated and logged to CRM",
      "Routed to the right fee earner",
    ],
  },
];
