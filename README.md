# ILIAS Plugin for OneDrive Business
##Prerequisite
------------
Go to "Administration->Repository->Repository Object Types" and make the cloud object available

##Installation
------------
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Modules/Cloud/CloudHook/
cd Customizing/global/plugins/Modules/Cloud/CloudHook/
git clone https://github.com/studer-raimann/OneDrive.git
```
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.

##Configration
Register a new App for OneDrive-business and use the credentials in the plugin administration. In the App-Setting you can use the follwing URL füre the OAuth-Redirect:
```bash
https://ilias.yourdomain.com/Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/redirect.php
```
###Contact
studer + raimann ag  
Waldeggstrasse 72  
3097 Liebefeld  
Switzerland 

info@studer-raimann.ch  
www.studer-raimann.ch  
