import type { UseCaseStory } from "@/types/content";

/**
 * Illustrative workflow examples of the type of system Strategro builds.
 * These are labelled explicitly as illustrative, not client results — see
 * each usage site for the accompanying disclosure.
 */
export const useCases: UseCaseStory[] = [
  {
    slug: "law-firm-intake",
    industry: "Law Firm",
    title: "New client intake, qualified before it reaches a fee earner",
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
  {
    slug: "property-enquiry-qualification",
    industry: "Real Estate",
    title: "Property enquiries qualified and matched before a call is booked",
    scenario:
      "A buyer enquires about a listing via WhatsApp on a weekend, when no agent is available to respond.",
    before: [
      "Message sits unanswered until Monday, and the buyer often enquires elsewhere",
      "Agents manually ask the same qualifying questions on every call",
      "No structured record of buyer requirements for future matching",
    ],
    after: [
      "WhatsApp chatbot responds immediately and captures budget, timeline, and requirements",
      "Qualified enquiries are routed to the right agent with full context",
      "Unqualified enquiries are logged for future follow-up rather than lost",
    ],
    systemFlow: [
      "WhatsApp enquiry received",
      "AI chatbot captures requirements and budget",
      "Lead scored and routed to matching agent",
      "Buyer profile logged for future listings",
    ],
  },
  {
    slug: "logistics-status-updates",
    industry: "Logistics",
    title: "Shipment status updates and documents handled without a phone queue",
    scenario:
      "A customer calls to ask where their shipment is, a question the operations team answers dozens of times a day.",
    before: [
      "Operations staff interrupt planning work to answer routine status calls",
      "Status information exists in the system but customers have no direct way to check it",
      "Delivery documents are chased manually by phone and email",
    ],
    after: [
      "Voice AI and chatbot answer routine status questions directly from the tracking system",
      "Document requests are captured and routed automatically to the right handler",
      "Operations staff only handle calls that genuinely need judgement",
    ],
    systemFlow: [
      "Customer contacts via call or chat",
      "AI checks live shipment status via workflow automation",
      "Answer given directly, or routed if action is needed",
      "Document requests logged and assigned",
    ],
  },
];
