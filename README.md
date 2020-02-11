ILIAS Plugin for OneDrive Business
----------------------------------
## Installation
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Modules/Cloud/CloudHook/
cd Customizing/global/plugins/Modules/Cloud/CloudHook/
git clone https://github.com/studer-raimann/OneDrive.git
```
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.

## Configuration in Microsoft Azure
To use the plugin, a new "App Registration" must be configured in your Azure "Active Directory". As 
"Redirect URI", enter the following (replace [your-ilias-url] with the url of your ILIAS installation): 

https://[your-ilias-url]/Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/redirect.php

Then enter the necessary Data from the registered app in the plugin's configuration.

### Adjustment suggestions
* Adjustment suggestions by pull requests
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/PLONEDRIVE
* Bug reports under https://jira.studer-raimann.ch/projects/PLONEDRIVE
* For external users you can report it at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_PLONEDRIVE

### ILIAS Plugin SLA
Wir lieben und leben die Philosophie von Open Source Software! Die meisten unserer Entwicklungen, welche wir im Kundenauftrag oder in Eigenleistung entwickeln, stellen wir öffentlich allen Interessierten kostenlos unter https://github.com/studer-raimann zur Verfügung.

Setzen Sie eines unserer Plugins professionell ein? Sichern Sie sich mittels SLA die termingerechte Verfügbarkeit dieses Plugins auch für die kommenden ILIAS Versionen. Informieren Sie sich hierzu unter https://studer-raimann.ch/produkte/ilias-plugins/plugin-sla.

Bitte beachten Sie, dass wir nur Institutionen, welche ein SLA abschliessen Unterstützung und Release-Pflege garantieren.
