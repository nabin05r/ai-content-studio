=== AI Content Studio ===
Contributors: nabinmagar
Tags: ai, content generator, gemini, image generation, ai writing
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate high-quality WordPress posts and images with 100% FREE AI-powered content using Google Gemini and Pollinations.ai.

== Description ==

AI Content Studio is a powerful WordPress plugin that helps you create professional blog posts and stunning images using completely free AI services. No subscription fees, no hidden costs - just powerful AI content generation at your fingertips.

= 🚀 Key Features =

* **100% FREE AI Services** - Uses Google Gemini for text and Pollinations.ai for images
* **Smart Content Generation** - Create full blog posts with proper HTML formatting
* **AI Image Generation** - Generate custom images with detailed prompts
* **Multiple Writing Tones** - Professional, casual, friendly, technical, creative, and formal
* **Flexible Word Counts** - Short (300-500), Medium (500-800), Long (800-1200), or Very Long (1200-2000 words)
* **Generation History** - Track all your AI-generated content and images
* **Rate Limiting** - Configurable daily limits to manage API usage
* **Media Library Integration** - Upload generated images directly to WordPress media library
* **User-Friendly Interface** - Clean, intuitive admin dashboard

= 🎯 Perfect For =

* Bloggers who need fresh content regularly
* Content marketers looking to scale production
* Website owners who want to save time writing
* Agencies managing multiple client websites
* Anyone who needs high-quality content quickly

= 🤖 AI Services Used =

**Google Gemini 2.5 Flash**
* Free tier with generous limits (60 requests/minute)
* Fast, high-quality text generation
* Supports multiple writing styles and tones
* Get your free API key at [Google AI Studio](https://aistudio.google.com/)

**Pollinations.ai**
* Completely free image generation
* Automatic Craiyon backup for reliability
* 1024x1024 image resolution
* No API key required

= 🔒 Privacy & Data =

This plugin sends content prompts to third-party AI services:
* **Google Gemini API** - For text content generation
* **Pollinations.ai** - For image generation

Your prompts and titles are sent to these services to generate content. Please review their privacy policies:
* [Google AI Terms](https://ai.google.dev/terms)
* [Pollinations.ai](https://pollinations.ai/)

No content is stored on external servers beyond the generation process.

= 🌍 Third-Party Services =

This plugin relies on the following external services:

**Google Gemini API**
* Service: https://generativelanguage.googleapis.com/
* Purpose: Generate AI-powered text content
* Terms of Service: https://ai.google.dev/terms
* Privacy Policy: https://policies.google.com/privacy

**Pollinations.ai**
* Service: https://image.pollinations.ai/
* Purpose: Generate AI-powered images
* Website: https://pollinations.ai/

**Craiyon API (Backup)**
* Service: https://api.craiyon.com/
* Purpose: Backup image generation service
* Website: https://www.craiyon.com/

Data sent to these services includes:
- Text prompts and titles for content generation
- Image descriptions for image generation

Please review each service's terms and privacy policy before using this plugin.

= 📝 How It Works =

1. Install and activate the plugin
2. Get your free Gemini API key from Google AI Studio
3. Enter your API key in Settings
4. Start generating content and images!

= 🎨 Features in Detail =

**Content Generation**
* Write a title and optional description
* Choose your preferred tone and word count
* Generate SEO-friendly, well-structured content
* Edit generated content before publishing
* Save as draft or publish immediately

**Image Generation**
* Describe your desired image in detail
* Free generation with Pollinations.ai
* Automatic backup with Craiyon if needed
* Download or upload to media library
* View generation history

**Dashboard**
* Track monthly content/image generation
* View average generation time
* Monitor API status
* Quick access to recent generations

**History & Analytics**
* Complete generation history
* Filter by provider and type
* Search by title
* Pagination support

= 🛠️ Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* Google Gemini API key (free from Google AI Studio)

= 🔗 Useful Links =

* [Get Free Gemini API Key](https://aistudio.google.com/)
* [Plugin Support](https://github.com/nabingm/ai-content-studio)

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "AI Content Studio"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded ZIP file and click "Install Now"
5. Click "Activate Plugin"

= Configuration =

1. After activation, go to AI Content Studio > Settings
2. Visit [Google AI Studio](https://aistudio.google.com/) to get your free API key
3. Copy your Gemini API key
4. Paste it into the "Gemini API Key" field in Settings
5. Click "Save Changes"
6. You're ready to generate content!

== Frequently Asked Questions ==

= Is this plugin really free? =

Yes! The plugin itself is free, and it uses completely free AI services:
* Google Gemini API has a generous free tier (60 requests/minute)
* Pollinations.ai is 100% free with no API key required

= Do I need an API key? =

You need a free Gemini API key from Google AI Studio for text generation. Image generation with Pollinations.ai requires no API key.

= How do I get a Gemini API key? =

1. Visit [Google AI Studio](https://aistudio.google.com/)
2. Sign in with your Google account
3. Click "Get API Key"
4. Create a new API key
5. Copy and paste it into AI Content Studio settings

It takes less than 2 minutes!

= What are the rate limits? =

* **Gemini Free Tier**: 60 requests per minute, 1,500 requests per day
* **Plugin Default**: 60 generations per user per day (configurable in settings)
* **Pollinations.ai**: No official limits, but please use reasonably

= Can I use this for commercial projects? =

Yes! Both Google Gemini's free tier and Pollinations.ai can be used for commercial projects. Please review their terms of service for details.

= What languages are supported? =

The plugin interface is in English, but Gemini AI can generate content in multiple languages. Simply write your prompt in your desired language.

= How long does generation take? =

* **Text Content**: Usually 5-15 seconds
* **Images**: 30-90 seconds (Pollinations.ai may take longer during peak times)

= Can I edit generated content before publishing? =

Yes! Generated content appears in an editor where you can review and edit before saving as a draft or publishing.

= What if image generation fails? =

The plugin automatically tries Pollinations.ai first, then falls back to Craiyon if needed. If both fail, you'll get a helpful error message. Simply wait 30 seconds and try again.

= Is my data private? =

Your prompts are sent to Google Gemini and Pollinations.ai to generate content. These services process your requests but don't store them long-term. Generated content is stored only in your WordPress database.

= Can I delete generation history? =

Currently, history is stored in your WordPress database. You can manually delete records from the history page in a future update.

= Does this work with Gutenberg? =

Yes! Generated content can be saved as posts and edited in any WordPress editor, including Gutenberg.

= Can I generate content in bulk? =

The current version generates one piece of content at a time. Bulk generation may be added in a future update.

== Screenshots ==

1. Dashboard - Overview of your AI content generation statistics
2. Content Generator - Create blog posts with customizable settings
3. Image Generator - Generate custom images with AI
4. Settings Page - Configure your API keys and preferences
5. Generation History - Track all your AI-generated content
6. Generated Content Preview - Review and edit before publishing

== Changelog ==

= 1.0.0 - 2025-01-21 =
* Initial release
* Google Gemini AI integration for text content
* Pollinations.ai integration for image generation
* Craiyon backup for image generation
* Dashboard with statistics
* Generation history with filtering
* Customizable writing tones and word counts
* Media library integration
* Rate limiting system
* Internationalization ready
* Security: Nonce verification and capability checks

== Upgrade Notice ==

= 1.0.0 =
Initial release of AI Content Studio. Start generating high-quality content with free AI services!

== Privacy Policy ==

AI Content Studio sends data to third-party services to generate content:

**Data Sent:**
* Content titles and descriptions (to Google Gemini)
* Image prompts (to Pollinations.ai and Craiyon)

**Data Storage:**
* Generated content is stored in your WordPress database
* Generation history (metadata only) is stored locally
* No API keys are transmitted to our servers
* API keys are stored in your WordPress database

**Third-Party Services:**
* Google Gemini API - [Privacy Policy](https://policies.google.com/privacy)
* Pollinations.ai - [Website](https://pollinations.ai/)
* Craiyon - [Website](https://www.craiyon.com/)

== Support ==

For support, feature requests, or bug reports:
* Visit our [GitHub repository](https://github.com/nabingm/ai-content-studio)
* Contact: https://nabinmagar.com

== Credits ==

* Developed by Nabin Gharti Magar
* Powered by Google Gemini AI
* Image generation by Pollinations.ai and Craiyon
* Icons from WordPress Dashicons

== Disclaimer ==

This plugin is not affiliated with, endorsed by, or sponsored by:
* Google LLC or Google Gemini
* Pollinations.ai
* Craiyon

All trademarks belong to their respective owners.
