---
name: Lumina Play
colors:
  surface: '#f6fafe'
  surface-dim: '#d6dade'
  surface-bright: '#f6fafe'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f4f8'
  surface-container: '#eaeef2'
  surface-container-high: '#e4e9ed'
  surface-container-highest: '#dfe3e7'
  on-surface: '#171c1f'
  on-surface-variant: '#4f4632'
  inverse-surface: '#2c3134'
  inverse-on-surface: '#edf1f5'
  outline: '#827660'
  outline-variant: '#d4c5ab'
  surface-tint: '#785900'
  primary: '#785900'
  on-primary: '#ffffff'
  primary-container: '#ffc107'
  on-primary-container: '#6d5100'
  inverse-primary: '#fabd00'
  secondary: '#006399'
  on-secondary: '#ffffff'
  secondary-container: '#04a8ff'
  on-secondary-container: '#003a5d'
  tertiary: '#006e1c'
  on-tertiary: '#ffffff'
  tertiary-container: '#7ce17b'
  on-tertiary-container: '#006419'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdf9e'
  primary-fixed-dim: '#fabd00'
  on-primary-fixed: '#261a00'
  on-primary-fixed-variant: '#5b4300'
  secondary-fixed: '#cde5ff'
  secondary-fixed-dim: '#95ccff'
  on-secondary-fixed: '#001d32'
  on-secondary-fixed-variant: '#004a75'
  tertiary-fixed: '#94f990'
  tertiary-fixed-dim: '#78dc77'
  on-tertiary-fixed: '#002204'
  on-tertiary-fixed-variant: '#005313'
  background: '#f6fafe'
  on-background: '#171c1f'
  surface-variant: '#dfe3e7'
typography:
  display-lg:
    fontFamily: Quicksand
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Quicksand
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Quicksand
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 30px
  title-md:
    fontFamily: Quicksand
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Quicksand
    fontSize: 18px
    fontWeight: '500'
    lineHeight: 26px
  body-md:
    fontFamily: Quicksand
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  label-lg:
    fontFamily: Quicksand
    fontSize: 14px
    fontWeight: '700'
    lineHeight: 20px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  gutter-mobile: 16px
  gutter-desktop: 24px
  margin-mobile: 20px
  safe-area: 24px
  tap-target-min: 48px
---

## Brand & Style

The brand personality is energetic, encouraging, and undeniably playful—designed to feel more like a premium mobile game than a traditional educational tool. The target audience is children aged 6-12, requiring a UI that prioritizes visual cues over dense text and celebrates every interaction with rewarding feedback.

The design style is **Tactile & Interactive**, characterized by:
- **Game-like Physics:** Elements feature "chunky" depth and physics-based micro-interactions.
- **Vibrant Nature Tones:** A palette inspired by sunny skies and lush landscapes to keep the mood light and optimistic.
- **High Clarity:** Information is compartmentalized into cards to prevent cognitive overload.
- **Bouncy Feedback:** Buttons and interactive zones utilize scale-up and scale-down animations to mimic physical toy properties.

## Colors

The palette is derived from the "Golden Reward" of the brand's logo and the "Open World" feel of the reference imagery.

- **Primary (Gold):** Used for rewards, achievements, and main action buttons to signify value and success.
- **Secondary (Sky Blue):** Used for navigation, headers, and secondary interactive elements to provide a calm, clear backdrop.
- **Tertiary (Grass Green):** Reserved for "Submit" or "Correct" states, reinforcing the connection to nature and growth.
- **Neutral (Cloud White/Soft Grey):** Used for card backgrounds and surfaces to ensure readability.
- **Backgrounds:** A soft gradient from `#E3F2FD` to white should be used for the main application background to create a sense of depth and airiness.

## Typography

Typography is exclusively **Quicksand**, chosen for its rounded terminals and friendly, approachable letterforms that are highly legible for young readers.

- **Headlines:** Use Bold (700) weights to create clear hierarchy. Large headlines should have a subtle 2px bottom offset shadow in a darker version of the text color to increase the "sticker" feel.
- **Body:** Use Medium (500) weights for better readability. Avoid Light weights as they lack the necessary contrast for younger eyes.
- **Accessibility:** Maintain a minimum font size of 16px for all body text to ensure ease of reading on mobile devices.

## Layout & Spacing

This design system uses a **Fluid Mobile-First Grid** that prioritizes large, accessible touch targets.

- **Model:** A single-column layout for mobile that expands to a 2-column masonry card layout for tablet and desktop.
- **Mobile Spacing:** 20px side margins ensure content doesn't hit the screen edges. Vertical spacing between quiz cards is set to 16px to maintain clear separation.
- **Tap Targets:** Every interactive element (checkboxes, buttons, radio options) must be at least 48px in height/width to accommodate smaller, less precise motor skills.
- **Zero Wasted Space:** Use full-bleed background illustrations, but keep core functional content centered in high-contrast containers.

## Elevation & Depth

Instead of realistic shadows, this system uses **Chunky Tonal Layers** and **Beveled Depths** to create a toy-like aesthetic.

- **The "3D" Effect:** Interactive elements feature a solid 4px-8px bottom border in a darker shade of the element's color (e.g., a Gold button has a Dark Gold bottom edge).
- **Pressed State:** When a user taps an element, it should translate 4px downward and its bottom border should disappear, simulating a physical button being pushed.
- **Card Depth:** Quiz cards use a soft, 10% opacity blue shadow (`#00A8FF`) rather than grey, keeping the UI "clean" and colorful.
- **Glassmorphism:** Use semi-transparent white (80% opacity) with a 10px backdrop blur for modal overlays and navigation bars to keep the background scenery visible.

## Shapes

The shape language is dominated by **Soft Geometry**. There are no sharp corners in this design system.

- **Base Radius:** 16px (1rem) for standard cards and buttons.
- **Large Radius:** 24px (1.5rem) for main containers and outer quiz wrappers.
- **Pill Shapes:** Used for status indicators, "Correct/Incorrect" chips, and score badges.
- **Checkboxes:** Instead of square boxes, use rounded-rectangles (8px radius) to maintain the soft aesthetic.

## Components

### Buttons
- **Chunky Primary:** Gold background, Dark Gold 4px bottom bevel, White text.
- **Submit Action:** Green background, Dark Green 4px bottom bevel.
- **Interaction:** On tap, scale down by 5% and remove the bevel.

### Quiz Cards
- White background with a 2px stroke of light blue (`#E3F2FD`).
- Header section of the card should have a contrasting blue top-bar with rounded top corners.

### Lists & Options
- Quiz options should be presented as large, tappable cards rather than simple text links.
- **Selected State:** Thick 3px border in Primary Gold with a checkmark icon appearing in the corner.

### Inputs & Selectors
- Text inputs feature a soft blue background and large font sizes. 
- Radio buttons are replaced by large "Option Tiles" to maximize tap area.

### Reward Toasts
- Pop-up notifications for correct answers should use "Bouncy" transitions (Spring physics) and include confetti or star particles.

### Progress Bar
- A thick, rounded track in light grey with a vibrant green "growing" fill that features a white "shine" highlight to look like a liquid tube.

### Interaction
- All clickable controls (links, buttons, checkbox/radio labels, selects) use `cursor: pointer`; disabled controls use `cursor: not-allowed`.
- Welcome home carousel: circular prev/next arrows, dot indicators, touch swipe on mobile, drag swipe on desktop.

### Staff navigation
- Admin and teacher detail pages use `<x-staff-nav-trail>` — breadcrumb path with ← back on parent steps (e.g. `Quizzes / Create quiz`).