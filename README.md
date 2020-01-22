# Phone Number Normalizator for Mautic

This plugin normalize phone numbers to E164 international format eg. +41446681800. By default try  normalize already recognized numbers (start with + etc), then applied you own defined rules for aby country from plugin settings. Others numbers are skipped.


## Installation

### Manual

1. Use last version
2. Unzip files to plugins/MauticPhoneNumberNormalizerBundle
3. Clear cache (app/cache/prod/)
4. Go to /s/plugins/reload

### Usage

1. Go to plugins and setup Phone Number Normalizer
2. Select phone fields to normalize
3. Set regex rules for location (If you want) - optional
4.Enable **Normalize before contact phone field change** If you want normalize phone number on each contact's phone number change
5. Enable **Normalize before SMS send to contact** If you want normalize number before SMS send. This option doesn't overwrite numbers, just  

### Start with regexp configuration  examples 

You can set rules in plugins settings. The first input is regexp for start with of phone numbers /^yourregexpfromsettings/. Second input is country code (US, SK, GB, FR, NL etc.)

<img src="https://user-images.githubusercontent.com/462477/72688499-2d0faa80-3b08-11ea-8416-76290625e937.png" width="500">

This definition set all phone numbers start with to Slovak phone numbers

- 901, 902, 903, 904, 905, 906, 907, 908, 909
- 911, 912, 913, 914, 915, 916, 917, 918, 919
- 0901, 0902, 0903, 0904, 0905, 0906, 0907, 0908, 0909
- 0911, 0912, 0913, 0914, 0915, 0916, 0917, 0918, 0919

Another example of regexp:

![image](https://user-images.githubusercontent.com/462477/72874080-80911c80-3cf1-11ea-8f42-4d3ab655c043.png)

This definition set all phone numbers start with to Netherlands numbers:
- 06, 6, 316
 
You can set any definition for any countries.
Your regexp can test on https://regex101.com/

### Command

`php app/console mautic:phone:number:normalize`

Process all contacts with phone numbers and normalize it. 

`php app/console mautic:phone:number:normalize --dry-run`

Process all contacts with phone number and display table before/after normalization

![image](https://user-images.githubusercontent.com/462477/72874633-c4385600-3cf2-11ea-9533-2986e091de47.png)

Optional parameters:

- --batch-limit (default 100)

## More Mautic stuff

- Plugins from Mautic Extendee Family  https://mtcextendee.com/plugins
- Mautic themes https://mtcextendee.com/themes

### Credits

Icons made by <a href="https://www.flaticon.com/authors/dinosoftlabs" title="DinosoftLabs">DinosoftLabs</a>