# AI Content Studio

![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress Compatibility](https://img.shields.io/badge/wordpress-5.8%2B-green.svg)
![PHP Version](https://img.shields.io/badge/php-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL%20v2%2B-red.svg)

**Generate high-quality WordPress posts and images with 100% FREE AI-powered content using Google Gemini and Pollinations.ai.**

## 🚀 Features

- **100% FREE AI Services** - Uses Google Gemini for text and Pollinations.ai for images
- **Smart Content Generation** - Create full blog posts with proper HTML formatting
- **AI Image Generation** - Generate custom images with detailed prompts
- **Multiple Writing Tones** - Professional, casual, friendly, technical, creative, and formal
- **Flexible Word Counts** - Short (300-500), Medium (500-800), Long (800-1200), or Very Long (1200-2000 words)
- **Generation History** - Track all your AI-generated content and images
- **Rate Limiting** - Configurable daily limits to manage API usage
- **Media Library Integration** - Upload generated images directly to WordPress media library
- **User-Friendly Interface** - Clean, intuitive admin dashboard

## 📋 Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Google Gemini API key (free from [Google AI Studio](https://aistudio.google.com/))

## 📦 Installation

### From WordPress Plugin Directory (Coming Soon)

1. Log in to your WordPress admin panel
2. Navigate to **Plugins** > **Add New**
3. Search for "AI Content Studio"
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the latest release from [GitHub Releases](https://github.com/nabingm/ai-content-studio/releases)
2. Upload the `ai-content-studio` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress

### From Source (Development)

```bash
cd wp-content/plugins/
git clone https://github.com/nabingm/ai-content-studio.git
```

Then activate the plugin in WordPress admin.

## ⚙️ Configuration

1. After activation, go to **AI Content Studio** > **Settings**
2. Visit [Google AI Studio](https://aistudio.google.com/) to get your free API key
3. Copy your Gemini API key
4. Paste it into the **Gemini API Key** field in Settings
5. Click **Save Changes**
6. Start generating content!

## 🎯 Usage

### Generate Text Content

1. Go to **AI Content Studio** > **Generate Content**
2. Enter your post title
3. (Optional) Add a description for more context
4. Choose your preferred:
   - **Tone**: Professional, Casual, Friendly, etc.
   - **Word Count**: Short, Medium, Long, or Very Long
5. Click **Generate Content**
6. Review and edit the generated content
7. Save as draft or publish immediately

### Generate Images

1. Go to **AI Content Studio** > **Generate Content** > **Image** tab
2. Describe your desired image in detail
3. Click **Generate Image**
4. Wait 30-90 seconds for generation
5. Download or upload to media library

## 🤖 AI Services Used

### Google Gemini 2.5 Flash
- Free tier with generous limits (60 requests/minute)
- Fast, high-quality text generation
- Supports multiple writing styles and tones
- Get your free API key at [Google AI Studio](https://aistudio.google.com/)

### Pollinations.ai
- Completely free image generation
- Automatic Craiyon backup for reliability
- 1024x1024 image resolution
- No API key required

## 📸 Screenshots

Coming soon...

## 🔒 Privacy & Third-Party Services

This plugin sends content prompts to third-party AI services:

- **Google Gemini API** - For text content generation
  - Service: `https://generativelanguage.googleapis.com/`
  - [Terms of Service](https://ai.google.dev/terms)
  - [Privacy Policy](https://policies.google.com/privacy)

- **Pollinations.ai** - For image generation
  - Service: `https://image.pollinations.ai/`
  - Website: [pollinations.ai](https://pollinations.ai/)

- **Craiyon API** - Backup image generation
  - Service: `https://api.craiyon.com/`
  - Website: [craiyon.com](https://www.craiyon.com/)

**Data sent to these services:**
- Text prompts and titles for content generation
- Image descriptions for image generation

No content is stored on external servers beyond the generation process.

## 🛠️ Development

### Project Structure

```
ai-content-studio/
├── admin/              # Admin interface
│   ├── ajax/          # AJAX handlers
│   ├── css/           # Admin styles
│   ├── js/            # Admin scripts
│   └── partials/      # Admin page templates
├── api/               # API integrations
│   ├── class-api-base.php
│   ├── class-gemini-api.php
│   └── class-pollinations-api.php
├── assets/            # Frontend assets
│   ├── css/
│   └── js/
├── includes/          # Core plugin classes
│   ├── class-ai-content-studio.php
│   ├── class-activator.php
│   ├── class-deactivator.php
│   └── helpers.php
├── languages/         # Translation files
├── ai-content-studio.php  # Main plugin file
└── readme.txt        # WordPress.org readme
```

### Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Changelog

### 1.0.0 - 2025-01-21
- Initial release
- Google Gemini AI integration for text content
- Pollinations.ai integration for image generation
- Craiyon backup for image generation
- Dashboard with statistics
- Generation history with filtering
- Customizable writing tones and word counts
- Media library integration
- Rate limiting system
- Security: Nonce verification and capability checks

## 🐛 Bug Reports & Support

If you find a bug or need support:
- [Open an issue](https://github.com/nabingm/ai-content-studio/issues)
- Visit: [nabinmagar.com](https://nabinmagar.com)

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
AI Content Studio - WordPress Plugin
Copyright (C) 2025 Nabin Gharti Magar

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## 👨‍💻 Author

**Nabin Gharti Magar**
- Website: [nabinmagar.com](https://nabinmagar.com)
- GitHub: [@nabingm](https://github.com/nabingm)

## 🙏 Credits

- Powered by Google Gemini AI
- Image generation by Pollinations.ai and Craiyon
- Icons from WordPress Dashicons

## ⚠️ Disclaimer

This plugin is not affiliated with, endorsed by, or sponsored by Google LLC, Google Gemini, Pollinations.ai, or Craiyon. All trademarks belong to their respective owners.

---

**Made with ❤️ by [Nabin Gharti Magar](https://nabinmagar.com)**
