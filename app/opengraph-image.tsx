import { ImageResponse } from "next/og";
import { siteConfig } from "@/lib/site-config";

export const alt = `${siteConfig.name} — ${siteConfig.tagline}`;
export const size = { width: 1200, height: 630 };
export const contentType = "image/png";

export default function Image() {
  return new ImageResponse(
    (
      <div
        style={{
          width: "100%",
          height: "100%",
          display: "flex",
          flexDirection: "column",
          justifyContent: "space-between",
          padding: 80,
          background: "#060f0d",
          backgroundImage:
            "radial-gradient(circle at 15% 15%, rgba(201,154,68,0.22), transparent 55%)",
        }}
      >
        <div style={{ display: "flex", alignItems: "center", gap: 16 }}>
          <div
            style={{
              display: "flex",
              width: 40,
              height: 40,
              borderRadius: 12,
              background: "#c99a44",
            }}
          />
          <div style={{ display: "flex", fontSize: 36, color: "#faf8f2", fontWeight: 600 }}>
            Strategro
          </div>
        </div>

        <div style={{ display: "flex", flexDirection: "column", maxWidth: 920 }}>
          <div
            style={{
              display: "flex",
              fontSize: 22,
              letterSpacing: 4,
              textTransform: "uppercase",
              color: "#c99a44",
              marginBottom: 24,
            }}
          >
            Signal to System
          </div>
          <div style={{ display: "flex", fontSize: 64, lineHeight: 1.15, color: "#faf8f2" }}>
            Strategies That Grow
          </div>
          <div style={{ display: "flex", fontSize: 28, color: "rgba(250,248,242,0.65)", marginTop: 24 }}>
            AI automation and data-driven growth systems
          </div>
        </div>
      </div>
    ),
    { ...size }
  );
}
