import type { NextConfig } from "next";

function wordpressImageHostname(): string | null {
  const apiUrl = process.env.WORDPRESS_API_URL;
  if (!apiUrl) return null;
  try {
    return new URL(apiUrl).hostname;
  } catch {
    return null;
  }
}

const wpHostname = wordpressImageHostname();

const nextConfig: NextConfig = {
  output: "standalone",
  images: {
    remotePatterns: [
      ...(wpHostname
        ? [
            { protocol: "https" as const, hostname: wpHostname },
            { protocol: "http" as const, hostname: wpHostname },
          ]
        : []),
    ],
  },
};

export default nextConfig;
