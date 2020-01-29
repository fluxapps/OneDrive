# Changelog

## [2.0.1]
- Bugfix: fixed file download
- Bugfix: creating root folder and subfolder at the same time didn't work
- Improvement: better error message when renaming fails
- Improvement: add file type suffix on rename
- Bugfix: Save token per user instead of per object (avoid "not authenticated")
- Bugfix: Added missing permission language variable ("hack" required)

## [2.0.0]
- Feature: "Open in Office Online" will automatically give permissions on document
- Feature: new permission for "Open in Office Online"
- Feature: renaming of folders and objects
- Feature: add file type suffix automatically if missing
- Feature: open base folder in Office Online via 'Actions' dropdown (in top right corner)
- Change: removed display of access token in object settings 
- Wording: different wording in object creation form
- Bugfix: fixed deep links
- Bugfix: catch unsupported characters exception
- Bugfix: catch title beginning with whitespace exception
- Bugfix: fixed objects beginning with an umlaut (created in ILIAS or OneDrive)
- Bugfix: fixed folders beginning with an umlaut (created in ILIAS or OneDrive)
- Bugfix: renaming of folder in OneDrive doesn't lead to an error anymore
- Bugfix: fixed ui representation of folders (less space, like in repository)

## [1.0.3]
- Add changelog

## [1.0.2]
- Update ILIAS support to 5.3 and 5.4
- Drop support for ILIAS 5.2 and below
- Add new permission "Open in Office Online"

## [1.0.1]
- Fix unsupported characters
- Fix cloud object starting with whitespace
- Fix cloud objects starting with umlaut
- Remove unnecessary section in settings
- Fix file upload without specified file extension
- Add proper description of base folder and custom folder on object creation
- Fix error on path rename in OneDrive

## [1.0.0]
- Release

## [0.1.0]
- First version
