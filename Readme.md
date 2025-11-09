# **Incident Report — PSK «realcodingguyy»**

**Internal ID:** `2025-09-30-PSK-codingguy`  
**Prepared by:** KaguyaLabs  
**Date:** 2025-09-30  
**Last Updated:** 2025-11-07  
**Case Status:** Ready for Closure

---

## **Executive Summary**

The subject **"realcodingguyy"** (identified as Robin Holzwarth) operated multiple fraudulent services including Fortnite cheats, credential stealers, and stolen YouTube account sales. The actor employed a consistent infrastructure pattern across all operations, using identical website designs and payment processing. Following comprehensive investigation, all major distribution channels have been dismantled, and remaining infrastructure is now dormant or non-responsive.

---

## **Confirmed Identity**

- **Legal Name:** Robin Holzwarth
    
- **Age:** 20+
    
- **Nationality:** German
    
- **Status:** Identified and documented
    

---

## **Identifiers / Contact Points**

### **Primary Aliases**

- **Telegram:** `reversedbytes`
    
- **Phone:** `+49 1512 9595059` _(T-Mobile DE)_
    

### **Discord IDs**

- `821765667127558144`
    
- `1421526524661596210`
    
- `1400605261865156850`
    
- `1396109560057430060`
    

### **Active Infrastructure**

- **YouTube:** `youtube.com/@BlackTuTandHacks` (limited content)
    
- **Telegram Bot:** `@TokGrabber_Bot` (visible but non-responsive)
    

### **Historical Infrastructure** _(All Inactive)_

- **Original Instance:** `89.144.15.131:2083` (first tokpanel malware builder)
    
- **Domains:** `tokpanel.cv`, `rezoncheats.com`, `webhook.my`, `bypassed.dev`
    
- **Telegram Channels:** `t.me/tokgrabber_chat`, `t.me/tokgrabber` (deleted)
    

### **Compromised TikTok Accounts**

- **Active/Used:** `@realcodingguyy`, `@ytaccs.com`, `@erenyeagerr06`, `@anime14809`, `@codingguy_top_tier`, `@nt_audio1`, `@kacper_official555`, `@og.falco`, `@derechtsepen.org`, `@flo_smc520rr`, `@this_is_jirka._`
    
- **Banned/Deleted:** `@stokarys_ninja`, `@diyromiocraft`, `@userxxgbuwbfce`
    

---

## **Infrastructure & Monetization Pattern**

The actor consistently reused the same technical and financial infrastructure across all fraudulent operations:

### **Common Infrastructure**

- **Payment Platform:** Paylix used for all services (`tokpanel.cv`, `rezoncheats.com`, `bypassed.dev`)
    
- **Website Design:** Identical template deployed across all domains
    
- **Hosting Pattern:** cPanel port copy (port 2083) for initial deployment, possibly used this method for hiding the builder.
    

### **Service Evolution**

1. **`89.144.15.131:2083`** - Original tokpanel malware builder (predates all domains)
    
2. **`tokpanel.cv`** - Fortnite cheat service with embedded malware
    
3. **`rezoncheats.com`** - Gaming cheat service continuation
    
4. **`bypassed.dev`** - Stolen YouTube account marketplace (offline as of Oct 28, 2025)
    

### **YouTube Account Black Market Connection**

- Sold stolen YouTube accounts using same infrastructure pattern
    
- Connected to criminal ecosystem where hijacked channels (100K+ subscribers)
    
---

## **Incident Description**

The actor operated multiple fraudulent services distributing malware and stolen accounts through evolving infrastructure.

### **Key Findings**

- Malware components were reused across builds: stealer.py / pyinstaller EXEs, `d-control` nuker, `tg.exe`, `tokenparser` scripts
    
- `webhook.my` was marketed as protection tool but embedded in malware for data exfiltration
    
- Actor admitted to theft and fraudulent activity using alternate accounts
    
- Consistent infrastructure pattern across all fraudulent operations
    
- All major servers/domains used to host services have been deleted or disabled
    
- Current infrastructure is dormant with bot non-responsive to commands
    

### **Threatening Communications**

**Voice Message Transcript (10-1-2025):**

> "Today a few lawyers, a few prosecutors, a few judges are being beaten up."

**Context:** Message sent shortly after investigative actions were initiated against the actor's infrastructure, indicating awareness of legal pressure.

---

## **Leaked Files / Source Artifacts**

All artifacts below were obtained via **mnx6**, a former collaborator of codingguyy:

### **Core Malware Components**

- **[webhook.my](https://webhook.my)** - Backend and frontend source code (PHP, JS, HTML) used for exfiltration.
    
- **Webhook builder/Stealer project** - Python builder using PyInstaller generating credential-stealing EXEs
    
- **d-control** - Windows Defender Controller (unverified, not fully analyzed)
    
- **tg.exe** - Telegram-related executable (not fully analyzed)
    

### **Supporting Tools**

- **[stealer.to](https://stealer.to)** - Python bot interacting with `webhook.my` endpoints (.blockpost, .blockcreate, .delete commands)
    
- **tokenparser** - Scripts (`scraper.py`, `sender.py`) collecting ~1,743 invalid Discord tokens
    

### **Miscellaneous Artifacts**

- `1.txt` - 100 MB, content unknown
    
- `dog.jpg` - Contains embedded Python code, not actual image data
    

---

## **Evidence Collected**

### **Complete Directory Structure**

```
├── Leaked Info/
│   ├── webhook/
│   │   ├── builder/ (PyInstaller build artifacts, main.exe, main.py)
│   │   ├── d control/ (dControl.exe, configuration files)
│   │   ├── nuker/ (main.exe, config.json)
│   │   ├── stealer.to/ (main.py bot)
│   │   ├── tg/ (tg.exe, session files, chat_id.txt)
│   │   └── tokenparser/ (scraper.py, sender.py, tokens.txt, 1,743 tokens)
│   └── webhook.my/
│       ├── backend/ (PHP scripts for data exfiltration)
│       └── frontend/ (HTML/JS interface)
├── main.md
├── photos/
│   ├── apartment/ (location imagery)
│   └── face/ (facial photographs)
├── Profile's/
│   ├── Discord/ (multiple account profiles and JSON data)
│   ├── epicnpc/ (EliReal profile)
│   ├── maybe/ (glideapps profile)
│   ├── playerrup/ (RealCodingGuy profile)
│   ├── Stolen/ (cryptosearching143 account)
│   └── tgstat/ (telegram statistics)
├── Rats/
│   ├── rezoncheats.com/ (malware executables and DLLs)
│   └── tokpanel.cv/ (malware executables and DLLs)
├── Screenshots/
│   ├── Admittion/ (7 admission screenshots)
│   ├── fake-giveaway/ (social engineering evidence)
│   ├── Logs/ (webhook logs)
│   ├── Website/ (rezoncheats.com, bypassed.dev, server screenshots)
│   └── youtube/ (JohnGartensi profile)
└── Voice Messages/
    ├── Chat transcript
    ├── Chat screenshot
    └── voice-message.ogg

```
### **Evidence Inventory**

#### **Malware Artifacts**

- **Builder Infrastructure**: Complete PyInstaller build environment with compiled executables
    
- **Webhook Systems**: Full backend/frontend code for `webhook.my` exfiltration service
    
- **Nuking Tools**: `nuker.exe` Discord Nuker, this has not been checked yet.

- **d-Control**: Windows Defender Disabler, this has not been checked yet.
    
- **Stealer Components**: Multiple Python-based stealers and token parsers
    
- **Telegram Integration**: `tg.exe` with session management

#### **Operational Intelligence**

- **Admission Evidence**: 7 screenshots showing actor admitting criminal activity
    
- **Profile Data**: Multiple Discord, EpicNPC, and social media profiles
    
- **Infrastructure Logs**: Server access logs and webhook captures
    
- **Social Engineering**: Fake giveaway campaigns and promotional materials
    

#### **Multimedia Evidence**

- **Visual Documentation**: Facial photographs and location imagery
    
- **Audio Evidence**: Voice messages with transcripts including threatening communications
    
- **Website Archives**: Complete screenshots of operational websites
    

#### **Malware Distribution**

- **RAT Executables**: Complete malware packages from `rezoncheats.com` and `tokpanel.cv`
    
- **DLL Components**: Supporting libraries and system files
    
- **Configuration Data**: Operational parameters and target lists
    

**Evidence Gaps:** Pre-token reset Discord DMs not captured. Collected evidence begins 9-29-2025.

---

## **Timeline**

| Date               | Event                                                                        |
| ------------------ | ---------------------------------------------------------------------------- |
| **Pre–Sep 2025**   | Actor runs initial malware service via `89.144.15.131:2083`                  |
| **Sep 29, 2025**   | Redirected webhook logs captured; evidence preserved                         |
| **Sep 30, 2025**   | Accessed actor Discord token; confirmed stolen accounts                      |
| **Oct 1, 2025**    | Admission screenshots and voice evidence collected; threatening message sent |
| **Early Oct 2025** | `tokpanel` removed; activity moves to `rezoncheats.com`                      |
| **Oct 11, 2025**   | Profile intelligence updated                                                 |
| **Oct 28, 2025**   | `bypassed.dev` taken offline (stolen YouTube account sales)                  |
| **Nov 7, 2025**    | Recovered leaks from `mnx6`                                                  |
| **Nov 7, 2025**    | Final status confirmed: bot visible but non-responsive                       |

---

## **Impact Assessment**

### **Historical Impact**

- **User Impact:** Credential theft affecting TikTok, Discord, Epic Games, and YouTube accounts
    
- **Platform Risk:** High - exfiltration tools embedded in multiple malware variants
    
- **Financial Impact:** Revenue generated through Paylix from multiple fraudulent services
    
- **Legal Exposure:** Clear violation of computer crime statutes (data theft, unauthorized access, threatening communications)
    

### **Current Status**

- **Threat Level:** Low (infrastructure dormant)
    
- **Operational Capacity:** Minimal (bot non-responsive)
    
- **Resurgence Potential:** Medium-High (infrastructure pattern established)
    
- **Recommendation:** Close with quarterly monitoring
    

---

## **Investigation Conclusion**

The investigation has successfully:

- [x]  Confirmed actor identity (Robin Holzwarth)
    
- [x]  Documented complete operational methodology and infrastructure pattern
    
- [x]  Tracked infrastructure evolution from initial IP to multiple domains
    
- [x]  Preserved comprehensive evidence archive including threatening communications
    
- [x]  Confirmed current dormant status across all services
    

**Final Assessment:** Actor has significantly reduced operational footprint following investigation. All major infrastructure is non-functional or dismantled. Consistent pattern of fraudulent service deployment documented. Case ready for closure with periodic monitoring.



# Disclaimer

All files and screenshots in this repository are collected for research, documentation, and educational purposes only. No ownership of third-party content is claimed, and these materials are not intended for malicious use.
