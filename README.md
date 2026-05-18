![Queens Botanical Garden — WordPress Block Theme](garden-banner.png)

# Queens Botanical Block Theme

Full Site Editing block theme for [Queens Botanical Garden](https://queensbotanical.org/). Global styles live in `theme.json`; layouts and templates are edited in the Site Editor.

## Try it in WordPress Playground

Launch a live demo (latest WordPress, theme installed and activated, Site Editor open):

**[Open demo in Playground →](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/azizramos00/qbg/main/_playground/blueprint.json)**

## Plugins (Playground demo)

The Playground blueprint installs and activates these plugins (matching the local QBG stack):

| Plugin | Slug |
|--------|------|
| Create Block Theme | `create-block-theme` |
| The Events Calendar | `the-events-calendar` |
| Spectra (Ultimate Addons for Gutenberg) | `ultimate-addons-for-gutenberg` |
| GTranslate | `gtranslate` |
| We're Open! (Opening Hours) | `opening-hours` |
| WordPress Importer | `wordpress-importer` |

To add or remove plugins, edit the `plugins` array in `_playground/blueprint.json`. Use a [wordpress.org slug](https://wordpress.org/plugins/) or a public zip URL for custom plugins.

Spectra’s setup wizard is skipped automatically via a blueprint step that marks onboarding complete.

## Requirements

- WordPress 6.4+
- PHP 8.1+

## Development

After theme changes, regenerate the Playground zip before pushing:

```bash
_playground/build-theme-zip.sh
```

Commit and push `_playground/queens-botanical-block-theme.zip` along with your other changes so the demo stays in sync.
