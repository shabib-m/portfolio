# Portfolio

Portfolio is a responsive, customizable portfolio theme for Backdrop CMS. It is designed for developers, designers, and other professionals who want to present their work, articles, and personal information in a clean, modern layout.

## Features

- Responsive portfolio layout with utility-based styling.
- Customizable theme appearance and social links.
- Optional import of the **Projects** and **Articles** taxonomy terms.
- Optional import of sample content, including three projects, three articles, and supporting profile/pages content (10 sample content items in total).
- Optional import of main navigation links.
- Views blocks for personal information, featured projects, and recent articles.
- Optional configuration of the home page layout.
- A taxonomy term listing View (`portfolio_taxonomy`) with a dedicated field template for category pages.
- No additional JavaScript libraries or third-party font dependencies are bundled.

## Requirements

- Backdrop CMS 1.x.
- Backdrop core modules used by the included content, fields, Views, taxonomy, menus, and layouts must be enabled as needed.

## Installation

1. Copy the `portfolio` directory into your Backdrop site's `themes` directory, or install the theme through the Backdrop appearance interface.
2. Go to **Administration > Appearance** (`admin/appearance`) and enable **Portfolio**.
3. Set Portfolio as the default theme if you want it to be used across the site.
4. Open the Portfolio theme settings page to customize its appearance.
5. On a new or test site, optionally select the import/setup actions you want, then save the settings.

**Important:** The sample content, taxonomy terms, menu links, Views blocks, and layout changes are optional setup actions. Review these actions before running them on an existing site, particularly if it already contains customized layouts or content.

## Theme setup

The theme settings provide optional actions to:

- Import the **Projects** and **Articles** taxonomy terms.
- Import sample content and associated images.
- Import navigation links.
- Import Views blocks for the homepage.
- Configure homepage layout blocks.

The import/setup functions are located in `includes/portfolio.setup.inc` and are loaded by the theme settings file. Setup actions should be run in the order presented in the theme settings interface. Completed imports are tracked to help prevent duplicates.

## Portfolio sections

The theme uses the `portfolio_main` taxonomy vocabulary with **Projects** and **Articles** terms. Category links may use the aliases `/projects` and `/articles`. The included `portfolio_taxonomy` View uses the `taxonomy/term/%` path and a contextual taxonomy term filter, so category listings do not require fixed term IDs.

The category cards are rendered with the dedicated template:

`templates/views/views-view-fields--portfolio-taxonomy--page.tpl.php`

## Customization

Use the theme settings page to configure appearance and links. Edit Views through **Administration > Structure > Views** and layouts through **Administration > Structure > Layouts**. Changes made through the administration interface are stored in the site's active configuration.

## License

The Portfolio theme code is distributed under the GNU General Public License, version 2 or later (GPL-2.0-or-later). See `LICENSE.txt` for the complete GPL version 2 text.

### Images and other assets

- **Sample avatar (`avatar.png`):** Adapted using ChatGPT image generation from a user-supplied image. The project maintainer must confirm rights to the original image and approve the license for the final image before publishing it. If the maintainer elects to license the image under GPL-2.0-or-later, update this paragraph to explicitly state that license.
- **Other bundled sample images:** Verify their provenance and licenses before publishing. Record their licenses here or in an accompanying attribution file.
- **Third-party fonts and JavaScript libraries:** None bundled, according to the maintainer's current review.

## Maintainers

- **Initial maintainer:** [Add maintainer name and GitHub profile before publication].

## Credits

- Developed for [Backdrop CMS](https://backdropcms.org/).
- Sample avatar adapted with OpenAI ChatGPT image-generation tools, subject to confirmation of the original image's rights.

## Support

Once published in Backdrop Contrib, report bugs and feature requests through the project's GitHub issue tracker.
