import { defineConfig, globalIgnores } from "eslint/config";
import nextVitals from "eslint-config-next/core-web-vitals";
import nextTs from "eslint-config-next/typescript";

const eslintConfig = defineConfig([
  ...nextVitals,
  ...nextTs,
  // Override default ignores of eslint-config-next.
  globalIgnores([
    // Default ignores of eslint-config-next:
    ".next/**",
    // WordPress theme + build scripts (plain browser JS / PHP, not part of the Next.js app)
    "wordpress/**",
    "out/**",
    "build/**",
    "next-env.d.ts",
    // Vendored Claude Code skill scripts — not part of the application build.
    ".claude/**",
  ]),
]);

export default eslintConfig;
