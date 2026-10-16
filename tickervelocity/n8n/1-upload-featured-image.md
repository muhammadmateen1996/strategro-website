# 1. Upload the featured image

HTTP Request node

- Method: `POST`
- URL: `https://YOUR-SITE/wp-json/wp/v2/media`
- Authentication: Basic Auth (WordPress username of the n8n user + its Application Password)
- Send Body: binary (the generated image)
- Headers:
  - `Content-Disposition`: `attachment; filename={{ $json.slug }}.webp`
  - `Content-Type`: `image/webp` (or `image/jpeg` / `image/png`)

Keep the returned `id`: it goes into `featured_media` in step 2.

Optional second call to set the alt text (good for SEO and accessibility):
`POST https://YOUR-SITE/wp-json/wp/v2/media/{id}` with `{ "alt_text": "Short description of the image" }`.
