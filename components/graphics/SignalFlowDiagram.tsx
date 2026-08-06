import { Calendar, Database, FileText, MessageSquare, Phone, TrendingUp, Users } from "lucide-react";

const inputs = [
  { Icon: Phone, label: "Calls", y: 8 },
  { Icon: MessageSquare, label: "Messages", y: 21 },
  { Icon: FileText, label: "Documents", y: 34 },
  { Icon: Users, label: "Leads", y: 47 },
  { Icon: Database, label: "Spreadsheets", y: 60 },
];

const outputs = [
  { Icon: Calendar, label: "Bookings", y: 16 },
  { Icon: Database, label: "Organised records", y: 34 },
  { Icon: TrendingUp, label: "Growth", y: 52 },
];

function inputPath(y: number) {
  return `M 12 ${y} C 30 ${y}, 34 34, 50 34`;
}

function outputPath(y: number) {
  return `M 50 34 C 66 34, 70 ${y}, 88 ${y}`;
}

export function SignalFlowDiagram() {
  return (
    <div
      className="relative aspect-[4/3] w-full max-w-xl sm:aspect-[16/10]"
      role="img"
      aria-label="Diagram showing disconnected calls, messages, documents, leads, and spreadsheets flowing into a central automated system, which produces organised bookings, records, and growth."
    >
      <svg viewBox="0 0 100 68" className="absolute inset-0 h-full w-full" aria-hidden="true">
        <g data-diagram-part="input-paths">
          {inputs.map((input, index) => (
            <path
              key={input.label}
              d={inputPath(input.y)}
              fill="none"
              stroke="var(--color-gold-500)"
              strokeOpacity="0.45"
              strokeWidth="0.5"
              strokeDasharray="1.5 2.2"
              className="animate-dashflow"
              style={{ animationDelay: `${index * 0.15}s` }}
            />
          ))}
        </g>
        <g data-diagram-part="output-paths">
          {outputs.map((output, index) => (
            <path
              key={output.label}
              d={outputPath(output.y)}
              fill="none"
              stroke="var(--color-gold-500)"
              strokeOpacity="0.7"
              strokeWidth="0.6"
              strokeDasharray="1.5 2.2"
              className="animate-dashflow"
              style={{ animationDelay: `${index * 0.2 + 0.3}s` }}
            />
          ))}
        </g>
        <g data-diagram-part="hub">
          <rect x="42" y="26" width="16" height="16" rx="4" className="fill-ink-800" stroke="var(--color-gold-500)" strokeWidth="0.4" />
          <circle cx="50" cy="34" r="2.2" className="fill-gold-500 animate-pulse-soft" />
        </g>
      </svg>

      {inputs.map((input, index) => (
        <IconNode
          key={input.label}
          icon={input.Icon}
          label={input.label}
          x={12}
          y={input.y}
          align="start"
          order={index}
        />
      ))}
      {outputs.map((output, index) => (
        <IconNode
          key={output.label}
          icon={output.Icon}
          label={output.label}
          x={88}
          y={output.y}
          align="end"
          order={index}
        />
      ))}
    </div>
  );
}

function IconNode({
  icon: Icon,
  label,
  x,
  y,
  align,
  order,
}: {
  icon: typeof Phone;
  label: string;
  x: number;
  y: number;
  align: "start" | "end";
  order: number;
}) {
  return (
    <div
      className="absolute -translate-y-1/2"
      style={{
        left: `${x}%`,
        top: `${(y / 68) * 100}%`,
        transform: "translate(-50%, -50%)",
      }}
    >
      <div
        data-diagram-part={align === "start" ? "input-node" : "output-node"}
        data-diagram-order={order}
        className="flex items-center gap-2"
        style={{ flexDirection: align === "end" ? "row-reverse" : "row" }}
      >
        <span className="flex size-8 shrink-0 items-center justify-center rounded-full border border-paper-50/15 bg-ink-900 text-gold-400 shadow-sm sm:size-9">
          <Icon className="size-3.5 sm:size-4" aria-hidden="true" />
        </span>
        <span className="hidden whitespace-nowrap text-[11px] font-medium text-paper-100/70 md:inline">
          {label}
        </span>
      </div>
    </div>
  );
}
