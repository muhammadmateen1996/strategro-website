import { Container } from "@/components/ui/Container";
import { Button } from "@/components/ui/Button";

export default function NotFound() {
  return (
    <section className="flex min-h-[70vh] items-center bg-ink-950 py-24">
      <Container className="text-center">
        <p className="font-display text-sm font-semibold uppercase tracking-[0.3em] text-gold-500">
          404
        </p>
        <h1 className="mt-4 font-display text-4xl text-paper-50 sm:text-5xl">
          This page isn&rsquo;t part of the system.
        </h1>
        <p className="mx-auto mt-5 max-w-lg text-paper-100/75">
          The page you&rsquo;re looking for may have moved or no longer exists. Try one of these
          instead.
        </p>
        <div className="mt-9 flex flex-wrap justify-center gap-4">
          <Button href="/">Back to homepage</Button>
          <Button href="/services" variant="secondary">
            Explore services
          </Button>
        </div>
      </Container>
    </section>
  );
}
