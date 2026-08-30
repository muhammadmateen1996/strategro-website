export default function Loading() {
  return (
    <div className="flex min-h-[60vh] items-center justify-center bg-paper-50">
      <span className="sr-only">Loading&hellip;</span>
      <div className="flex items-center gap-2" aria-hidden="true">
        <span className="size-2.5 animate-pulse-soft rounded-full bg-gold-500 [animation-delay:-0.32s]" />
        <span className="size-2.5 animate-pulse-soft rounded-full bg-gold-500 [animation-delay:-0.16s]" />
        <span className="size-2.5 animate-pulse-soft rounded-full bg-gold-500" />
      </div>
    </div>
  );
}
