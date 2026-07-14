# Changelog

### 1.8.1 (2026-07-14)

#### Bug Fixes

- inserter: rename data-jq-version to data-version (73a4bfe)

## v1.8.0 (2026-07-14)

#### Features

- refresh quests from inserter block (44f6f9d)
- api: update organization games endpoint and loader versioning (575297e)
- support v2 quests in selectors and embeds (3f04c39)

#### Bug Fixes

- jquest-inserter: update dashboard URL generation logic (4de07cf)
- sync script version with v2 quests (2a158cd)
- hide script version for v2 quests (db24779)

#### Styles

- polish inserter quest controls (ac3bea6)

## v1.7.0 (2026-07-13)

#### Features

- jquest-inserter: This is a feat to bump version correctly, fixed lint stuff (91b20f1)

#### Refactor

- scripts: consolidate script versioning to global setting (7a3a049)

### v1.6.1 (2026-06-18)

#### Maintenance

- build: update dist ignore and slack channel settings (259816b)

## v1.6.0 (2026-06-18)

#### Features

- plugin: add update mechanism and remove dev dependencies (269faee)

#### Styles

- scripts: apply WordPress coding standards to script functions (3140e39)

#### Build System

- ci: update release workflow and environment configuration (4a3a20f)

#### Continuous Integration

- github: add composer to release workflow and update check dependency (cfe398a)

#### Maintenance

- config: add and configure phpcs.xml ruleset (1f671fc)
- build: setup linting, add checks, and clean up files (610277c)
- languages: remove outdated pot file (2a3faf4)
- vendor: remove composer autoloader files (64569b7)
- build: rename exclusion file and remove redundant documentation (22aca6b)

### v1.5.1 (2026-06-16)

#### Bug Fixes

- fixed way jquest script is loaded into site (5ba9b8f)

## v1.5.0 (2026-04-22)

#### Features

- minimization behavior (35eab82)
- able to add individual trigger buttons from jquest gutenberg block (ff8645d)

## v1.4.0 (2026-03-18)

#### Features

- preview area in settings (4537647)
- More customization settings (ed4ecbb)

#### Bug Fixes

- cookieyes cookie ignore attribute (d587da9)

### v1.3.1 (2026-03-18)

#### Features

- changelog: automate generation via conventional-changelog (d6ffbf6)

#### Bug Fixes

- test? (b975691)
- cookiesYes ignore support (a5afdf6)

#### Build System

- makefile: remove vendor directory in clean target (63eba5e)
- release: Add makefile and scripts to create a release on github. (e4787a3)

#### Maintenance

- clean vendor folder (e1acf7e)

## v1.3.0 (2026-03-14)

#### Bug Fixes

- styling changes + sectioned trigger colors (82ca356)

### v1.2.4 (2026-03-14)

#### Bug Fixes

- no polylang (19d36f8)

#### Maintenance

- pot? (66dc825)

### v1.2.3 (2026-03-14)

#### Features

- Trigger customization added (0f3700f)
- popup settings (b4ea751)

#### Styles

- added styles to admin (7282adc)

#### Maintenance

- gitignore change (4cba845)
- removed deprecated file (2330871)

### Vibe
- cleanup (eca2ec2)

### v1.1.2 (2026-01-11)

#### Bug Fixes

- changed cookieconsent attribute on script element (75f687a)

#### Maintenance

- patch (688bc77)

## v1.1.0 (2025-12-04)

#### Features

- added more data attributes + fixed slight bug in script insertion (e341bad)

## v1.0.0 (2025-11-17)

#### Features

- new functions (ac6c17f)

#### Bug Fixes

- removed print (ea1d22a)

### v0.5.6 (2025-09-22)

#### Bug Fixes

- dont crash on maybe_insert_jquest_scripts (3042978)

### v0.5.5 (2025-06-06)

#### Bug Fixes

- fixed bug where script didnt load (c462723)

#### Maintenance

- ? (0fd8793)
- version (7e4369a)

### v0.5.2 (2025-05-16)

#### Maintenance

- include vendor folder (77af58b)

### Hotfix
- include vendor file for the time being. 🚑 (e131317)

### v0.5.1 (2025-05-16)

#### Maintenance

- add .history directory to Gruntfile exclusion list (1749f71)
- update Gruntfile to exclude .history directory from processing (e195570)

### Chose
- add comment to clarify default attribute handling in JQUEST block rendering (d5b5a64)

## v0.5.0 (2025-05-16)

#### Features

- rework script handling for JQUEST blocks, move the script from the block to the head. ✨ (b9a8acc)

#### Refactor

- clean up code formatting and improve consistency in consts, options, and table classes ♻️ (b7789e0)

#### Maintenance

- update .gitignore to include /vendor/ directory (d9736e8)
- remove vendor from git and remove unused deps (0e57cbb)

### v0.4.7 (2025-05-15)

#### Bug Fixes

- update version script to include pnpm start (38222e8)
- correct filename (fa99088)

#### Maintenance

- bump version to 0.4.6 and update readme filename (b25ab48)

### v0.4.5 (2025-05-15)

#### Features

- Add versionSync script to update plugin version in files ✨ (2cda367)

### v0.4.4 (2025-05-13)

#### Styles

- fix styles (feed887)

### v0.4.3 (2025-05-12)

#### Features

- edit in dash button added (b314910)

#### Styles

- logo and colors to match branding (1fafdb5)

#### Build System

- Add postVersion script to npm scripts. (8c32b95)

### v0.4.1 (2025-01-17)

#### Features

- Set correct version in package.json (7fc609f)

#### Bug Fixes

- Fixed a typo in link to settings page. 🐛 ✏ (0dbb734)

## v0.4.0 (2025-01-13)

#### Features

- Removed Strauss & vendor prefixed. (c09724c)
- Removed Strauss & vendor prefixed. (5c65d24)
- WP_List_Table (05439a4)
- Refresh quest list functionality added. (8b32b2f)
- jquest inserter block changed to support new styles & staging. (0aaee40)
- Added checkboxes to block editor. (256de4d)
- Display games (af4612c)
- Functionality to set org id (6209a7e)
- Started reworking plugin without timber. (da60df5)

#### Bug Fixes

- rekai -> jquest (212b9ed)

#### Maintenance

- removed space from package.json (3a01f5a)
- Removed more stuff I think isnt needed. (c8ddaf8)
- Removing bloat that made me confused that I dont think is needed. (fbc038a)
- I dont think these are required.. SO REMOVEEEE 2.0 Electric boogaloo (c561f08)
- I dont think these are required.. SO REMOVEEEE (4ada121)

### v0.3.6 (2024-10-12)

#### Features

- refesh quest button (305b56c)

#### Bug Fixes

- I think these files should be added? As vendor is wanted? (8afc73b)
- Trying to get plugins to work 5.5 (237fbaf)

### Stash
- SO I can access from laptop. (598d0b7)

### v0.3.5 (2024-10-01)

### Misc
- removed vendor prefixed, its buggy (e4197a7)

### v0.3.4 (2024-09-24)

#### Bug Fixes

- Vendor (c21ac4d)

### v0.3.3 (2024-08-08)

### v0.3.2 (2024-08-08)

#### Bug Fixes

- ??? test (might be bad code) (913be11)

### v0.3.1 (2024-08-08)

#### Bug Fixes

- circumvent cookiebot as no cookies are stored by script. (304b1d7)

## v0.3.0 (2024-06-04)

#### Refactor

- firebase: Fixed an issue where the organization games were not being updated ♻️ (3a93551)

#### Maintenance

- release: version 0.3.0 🔖 (6e8ab91)
- Config stuff 🔧 (9fcfdeb)

## v0.2.0 (2024-06-04)

#### Refactor

- updated the loading of JQUEST to work correctly. ♻ (178cfe2)

#### Maintenance

- clean up (9b88fb1)
- project settings and config updated 🔧 (4f18c45)

### v0.1.3 (2024-02-08)

#### Maintenance

- added vendor and vendor-prefixed folders to repo (0b51a2b)

### v0.1.2 (2024-02-07)

#### Bug Fixes

- changed type to wordpress-plugin and modified authors 🩹 (81ea793)

### v0.1.1 (2024-02-07)

#### Maintenance

- change name to jquest-plugin 🔧 (46e9f7b)

### Misc
- Initial commit (f05b54d)

