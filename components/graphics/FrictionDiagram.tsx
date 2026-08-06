const chaosNodes = [
  { x: 10, y: 12 },
  { x: 26, y: 30 },
  { x: 8, y: 46 },
  { x: 30, y: 8 },
  { x: 20, y: 58 },
  { x: 34, y: 42 },
];

const chaosLines = [
  "M10 12 L26 30",
  "M26 30 L8 46",
  "M30 8 L20 58",
  "M8 46 L34 42",
  "M10 12 L30 8",
  "M20 58 L26 30",
];

const orderRows = [14, 30, 46, 62];

export function FrictionDiagram() {
  return (
    <div
      className="relative aspect-[16/9] w-full max-w-2xl"
      role="img"
      aria-label="Diagram showing scattered, disconnected operational tasks on the left resolving into an ordered, structured workflow on the right."
    >
      <svg viewBox="0 0 100 68" className="h-full w-full" aria-hidden="true">
        <g opacity="0.55">
          {chaosLines.map((d) => (
            <path key={d} d={d} stroke="var(--color-ink-500)" strokeWidth="0.4" fill="none" />
          ))}
          {chaosNodes.map((node) => (
            <circle key={`${node.x}-${node.y}`} cx={node.x} cy={node.y} r="1.6" className="fill-ink-600" />
          ))}
        </g>

        <path
          d="M40 34 H60"
          stroke="var(--color-gold-500)"
          strokeWidth="0.5"
          strokeDasharray="1.5 2"
          className="animate-dashflow"
        />

        <g>
          {orderRows.map((y, index) => (
            <g key={y}>
              <line x1="66" y1={y} x2="94" y2={y} stroke="var(--color-ink-600)" strokeWidth="0.35" />
              <circle cx="66" cy={y} r="1.4" className="fill-gold-500" style={{ opacity: 1 - index * 0.12 }} />
            </g>
          ))}
        </g>
      </svg>
    </div>
  );
}
