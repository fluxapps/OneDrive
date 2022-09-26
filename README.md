# OneDrive

Allows the connection of a OneDrive platform to ILIAS LMS through ILIAS' Cloud Objects. These are the main features:
* Browse, create/delete folders, upload/download/delete files directly from/to OneDrive
* Choose a root folder
* *OAuth2* Authentication
* Fast download, avoiding php time/memory limit, no temporary files

## Getting Started

### Requirements

* ILIAS 6.x / 7.x

### Installation

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Modules/Cloud/CloudHook/
cd Customizing/global/plugins/Modules/Cloud/CloudHook/
git clone https://github.com/studer-raimann/OneDrive.git
```
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.

### Configuration in Microsoft Azure

#### App Registration

To use the plugin, a new "App Registration" must be configured in your Azure "Active Directory". As 
"Redirect URI", enter the following (replace [your-ilias-url] with the url of your ILIAS installation): 

https://[your-ilias-url]/Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/redirect.php

Then enter the necessary Data from the registered app in the plugin's configuration.

#### Sharing Settings

The plugin creates a sharing link with viewer permissions for created folders. Therefore, the OneDrive's (or Sharepoint) settings may have to be adjusted to allow sharing. To do that, open the OneDrive admin center and choose "Sharing" (should be at https://admin.onedrive.com/?v=SharingSettings). Look for "External Sharing" and set the regulator to "Anyone (Users can create shareable links that don't require sign-in)". 

#### Permissions

In the registered App, open the "API Permissions" and add the following permissions:
* Azure Active Directory Graph (1)
    * User.Read
* SharePoint (2)
    * MyFiles.Read
    * MyFiles.Write

## Maintenance
This is an OpenSource project by fluxlabs ag, support@fluxlabs.ch

This project is maintained by fluxlabs. 

