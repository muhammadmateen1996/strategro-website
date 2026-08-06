import type { BlogPost } from "@/types/wordpress";

/**
 * Local fallback content used when WORDPRESS_API_URL is not configured or the
 * WordPress REST API is unreachable, so the site renders a populated blog
 * before headless WordPress is connected. Clearly not live editorial content.
 */
export const samplePosts: BlogPost[] = [
  {
    id: "sample-1",
    slug: "why-lead-response-speed-decides-more-deals-than-price",
    title: "Why Lead Response Speed Decides More Deals Than Price",
    excerpt:
      "Businesses that reply to a new enquiry within minutes convert at a materially higher rate than those that reply within hours. Here is what that means for how you route leads.",
    content: `<p>Most businesses lose deals not on price, but on speed. A prospect who fills in a form or sends a WhatsApp message is comparing you to whoever answers first, not necessarily whoever is cheapest.</p>
<h2>Where the delay actually happens</h2>
<p>The gap is rarely a willingness problem. It is a routing problem: enquiries sit in a shared inbox, a CRM notification gets missed, or a call comes in outside office hours and simply goes unanswered.</p>
<h2>What a response system looks like</h2>
<p>A workflow that captures the enquiry, qualifies it against your criteria, and routes it to the right person or system in seconds &mdash; rather than hours &mdash; changes the maths of your pipeline without changing your pricing.</p>
<h2>Where to start</h2>
<p>Map every channel a lead can arrive through: web form, phone, WhatsApp, email. For each one, ask how long it currently takes for a human to see it. That gap is your starting point.</p>`,
    date: "2026-06-02T09:00:00.000Z",
    modifiedDate: "2026-06-02T09:00:00.000Z",
    author: "Strategro Team",
    categories: ["AI Automation", "Sales Operations"],
    featuredImage: null,
    source: "sample",
  },
  {
    id: "sample-2",
    slug: "what-a-rag-knowledge-assistant-actually-needs-to-work",
    title: "What a RAG Knowledge Assistant Actually Needs to Work",
    excerpt:
      "Retrieval-augmented generation sounds simple in theory. In practice, the quality of a knowledge assistant depends on decisions made long before a user asks a question.",
    content: `<p>A knowledge assistant trained on your company documents is only as reliable as the retrieval system behind it. The model is rarely the weak link &mdash; document structure and indexing usually are.</p>
<h2>Document hygiene comes first</h2>
<p>Outdated policies, duplicate versions, and inconsistent formatting all degrade answer quality. Before connecting any documents, it is worth auditing what actually needs to be searchable.</p>
<h2>Grounding answers in sources</h2>
<p>A well-built assistant should cite the document it drew an answer from, and decline to answer when nothing relevant is found, rather than guessing.</p>
<h2>Where this fits operationally</h2>
<p>Internal teams typically use this to reduce repeated questions to specialists. Customer-facing use needs tighter guardrails and a clear escalation path to a human.</p>`,
    date: "2026-05-14T09:00:00.000Z",
    modifiedDate: "2026-05-14T09:00:00.000Z",
    author: "Strategro Team",
    categories: ["RAG", "AI Systems"],
    featuredImage: null,
    source: "sample",
  },
  {
    id: "sample-3",
    slug: "voice-ai-receptionists-what-they-can-and-cannot-do-yet",
    title: "Voice AI Receptionists: What They Can and Cannot Do Yet",
    excerpt:
      "Voice AI can reliably answer calls, capture details, and book appointments. It is not a replacement for judgement calls that genuinely need a person. Here is where the line sits today.",
    content: `<p>Voice AI receptionists have matured enough to handle structured call flows well: answering, qualifying, booking, and routing. Where they still fall short is unscripted, high-stakes conversation.</p>
<h2>Good fits</h2>
<p>Appointment booking, availability checks, standard intake questions, and after-hours call capture are all strong fits, because the conversation follows a predictable shape.</p>
<h2>Weak fits</h2>
<p>Sensitive complaints, negotiation, or anything requiring discretion should route to a person quickly, with the AI handling capture and handoff rather than resolution.</p>
<h2>Measuring it properly</h2>
<p>The right metric is not "calls handled" but missed-call reduction and time-to-booking, since those map directly to revenue.</p>`,
    date: "2026-04-21T09:00:00.000Z",
    modifiedDate: "2026-04-21T09:00:00.000Z",
    author: "Strategro Team",
    categories: ["Voice AI", "Operations"],
    featuredImage: null,
    source: "sample",
  },
];
