---
name: Clinical Clarity
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#434653'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#737784'
  outline-variant: '#c3c6d5'
  surface-tint: '#1d59c1'
  primary: '#003c90'
  on-primary: '#ffffff'
  primary-container: '#0f52ba'
  on-primary-container: '#bcceff'
  inverse-primary: '#b0c6ff'
  secondary: '#006c49'
  on-secondary: '#ffffff'
  secondary-container: '#6cf8bb'
  on-secondary-container: '#00714d'
  tertiary: '#5c3800'
  on-tertiary: '#ffffff'
  tertiary-container: '#7c4d00'
  on-tertiary-container: '#ffc278'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d9e2ff'
  primary-fixed-dim: '#b0c6ff'
  on-primary-fixed: '#001945'
  on-primary-fixed-variant: '#00419c'
  secondary-fixed: '#6ffbbe'
  secondary-fixed-dim: '#4edea3'
  on-secondary-fixed: '#002113'
  on-secondary-fixed-variant: '#005236'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  title-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-rt:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-mono:
    fontFamily: JetBrains Mono
    fontSize: 14px
    fontWeight: '450'
    lineHeight: 20px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  container-max: 1440px
  gutter: 20px
---

## Brand & Style
The design system is engineered for high-stakes clinical environments where cognitive load must be minimized. The brand personality is **Professional, Dependable, and Compassionate**. It balances the sterile precision required for medical data with an approachable warmth that reduces user fatigue.

The design style follows a **Modern Corporate** aesthetic with a lean toward **Minimalism**. It prioritizes information density without clutter, using generous whitespace to isolate critical patient data. The interface feels like a high-end diagnostic tool: quiet, efficient, and secondary to the content it hosts.

## Colors
The palette is rooted in "Clinical Blue," a shade chosen for its associations with trust and stability in healthcare. 

- **Primary (Clinical Blue):** Used for primary actions, active navigation states, and brand identifiers.
- **Success (Green):** Specifically for "Stable," "Completed," or "Verified" statuses.
- **Warning (Amber):** Reserved for "Pending," "Elevated Risk," or "Urgent Review" notifications.
- **Surface & Backgrounds:** Use a scale of Cool Grays (#F8FAFC to #1E293B). Backgrounds should remain off-white to reduce screen glare during long shifts.

## Typography
**Inter** is the workhorse of the design system, chosen for its exceptional legibility in data-heavy contexts and its tall x-height. 

- **Hierarchy:** Use `display-lg` sparingly for dashboard overviews. `title-sm` is the default for card headers and section titles.
- **Data Display:** For numerical values, patient IDs, or lab results, utilize a monospaced alternative (JetBrains Mono) to ensure tabular alignment and clear character distinction (e.g., O vs 0).
- **Readability:** Maintain a minimum of 1.5 line-height for body text to assist doctors in scanning long clinical notes.

## Layout & Spacing
The design system utilizes a **12-column fluid grid** for desktop and a **6-column grid** for tablets. 

- **Rhythm:** A 4px baseline grid governs all vertical rhythm.
- **Margins:** Desktop views should maintain 32px outer margins, while tablet views compress to 16px to maximize data real estate.
- **Density:** Provide two density modes: "Standard" for administrative tasks and "Compact" for clinical data tables to allow more rows per screen.
- **Safe Areas:** Key actions (Save, Prescribe) must be anchored to the bottom-right on tablet views for easy thumb access.

## Elevation & Depth
Depth is used functionally to indicate interactivity and information priority, not for decoration.

- **Surface Levels:** The primary background is the lowest level. White "cards" sit on top to group related patient information.
- **Shadows:** Use extremely soft, low-opacity indigo-tinted shadows (e.g., `rgba(15, 82, 186, 0.05)`) for cards. 
- **Active States:** Elevate elements slightly on hover (+2px Y-offset) to provide tactile feedback.
- **Modals:** Use a heavy backdrop blur (8px) to isolate critical diagnostic workflows or prescription confirmations from the background data.

## Shapes
The design system employs **Rounded** geometry (8px default) to soften the "clinical" feel and make the software feel more modern and accessible.

- **Buttons & Inputs:** Use the standard 8px (0.5rem) radius.
- **Status Tags/Chips:** Use 16px (1rem) or fully pill-shaped corners to distinguish them from interactive buttons.
- **Data Containers:** Large cards housing patient charts should use 12px (0.75rem) to create a clear structural frame.

## Components
- **Data Tables:** The core of the system. Use sticky headers and "zebra-striping" on hover. Include a "Priority" column that uses colored pips (Amber/Red) for triage.
- **Action Buttons:** Primary buttons use the Clinical Blue fill. Ghost buttons are preferred for "Cancel" or "Go Back" to reduce visual noise.
- **Input Fields:** Use floating labels to maintain context. Validation states must include both a color change (Red) and an icon (Alert) for accessibility.
- **Patient Header:** A persistent, high-contrast bar at the top of patient records containing Name, DOB, MRN, and Allergies (highlighted in Warning Amber).
- **Timeline/History:** A vertical list component with threaded icons to track patient visits, medications, and lab results chronologically.
- **Icons:** Use Lucide icons with a 1.5px stroke weight for a clean, balanced look that matches the Inter typeface.