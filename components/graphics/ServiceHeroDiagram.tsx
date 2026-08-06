"use client";

import {
  CalendarCheck,
  CheckCircle2,
  ClipboardList,
  Database,
  FileCheck2,
  Mail,
  Mic,
  PhoneIncoming,
  Sparkles,
  Workflow,
} from "lucide-react";
import { ServiceFlowDiagram } from "@/components/graphics/ServiceFlowDiagram";
import { ChatBubbleDiagram } from "@/components/graphics/ChatBubbleDiagram";
import { DocumentAnswerDiagram } from "@/components/graphics/DocumentAnswerDiagram";

export function ServiceHeroDiagram({ slug }: { slug: string }) {
  switch (slug) {
    case "ai-workflow-automation":
      return (
        <ServiceFlowDiagram
          ariaLabel="Diagram showing email and CRM data flowing through an automated workflow into a synced, up-to-date system."
          stages={[
            { icon: Mail, label: "Email" },
            { icon: Database, label: "CRM" },
            { icon: Workflow, label: "Automation", emphasize: true },
            { icon: CheckCircle2, label: "Synced" },
          ]}
        />
      );
    case "ai-chatbots":
      return <ChatBubbleDiagram />;
    case "voice-ai-receptionists":
      return (
        <ServiceFlowDiagram
          ariaLabel="Diagram showing an incoming call answered by a voice AI receptionist, resulting in a booked appointment."
          stages={[
            { icon: PhoneIncoming, label: "Call" },
            { icon: Mic, label: "Voice AI", emphasize: true },
            { icon: CalendarCheck, label: "Booked" },
          ]}
        />
      );
    case "rag-knowledge-assistants":
      return <DocumentAnswerDiagram />;
    case "ai-lead-content-systems":
      return (
        <ServiceFlowDiagram
          ariaLabel="Diagram showing a captured lead qualified by AI and turned into a generated proposal document."
          stages={[
            { icon: ClipboardList, label: "Lead" },
            { icon: Sparkles, label: "AI Qualifies", emphasize: true },
            { icon: FileCheck2, label: "Proposal" },
          ]}
        />
      );
    default:
      return null;
  }
}
