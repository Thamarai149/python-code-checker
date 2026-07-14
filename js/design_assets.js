// ============================================================
//  Nexus Studio — Design System v2.0
//  Updated color palette, typography, components & flow SVG
// ============================================================
const DesignSystem = {
    theme: {
        colors: [
            { name: "Background Deep",   value: "#080c12",                         description: "Primary app backdrop — near-black deep space" },
            { name: "Background Studio", value: "#0e1420",                         description: "Sidebar, toolbar & panel surfaces" },
            { name: "Primary Accent",    value: "#4f9eff",                         description: "Buttons, links, active indicators, glows" },
            { name: "Secondary Blue",    value: "#1a5fd1",                         description: "Gradient ends, secondary interactive elements" },
            { name: "Accent Violet",     value: "#a78bfa",                         description: "Highlights, gradient accents, AI panel" },
            { name: "Accent Cyan",       value: "#22d3ee",                         description: "Code snippets, monospace highlights" },
            { name: "Success Green",     value: "#34d399",                         description: "Successful runs, solved status, online presence" },
            { name: "Error Red",         value: "#f87171",                         description: "Compile failures, critical alerts, stop state" },
            { name: "Warning Amber",     value: "#fbbf24",                         description: "Medium-difficulty badges, warnings" },
            { name: "Text Main",         value: "#c8d3e0",                         description: "Default readable content text" },
            { name: "Text Dim",          value: "#6b7a90",                         description: "Labels, meta text, placeholders" },
            { name: "Glass Border",      value: "rgba(255, 255, 255, 0.07)",        description: "Subtle panel and card dividers" },
            { name: "Console Dark",      value: "#020508",                         description: "Editor and terminal backdrop" }
        ],
        typography: {
            fonts: [
                { name: "Interface Font",  family: "'Inter', system-ui, sans-serif",        usage: "All UI text, labels, inputs, paragraphs" },
                { name: "Accent / Brand",  family: "'Outfit', sans-serif",                  usage: "Page titles, card headings, logo, badges" },
                { name: "Monospace Code",  family: "'Fira Code', 'Cascadia Code', monospace", usage: "Code editor, terminal output, inline code" }
            ]
        }
    },

    components: [
        {
            name: "Primary Run Button",
            html: `<button class="nx-btn nx-btn-run"><i class="fa-solid fa-play"></i> Run Code</button>`,
            code: `<button class="nx-btn nx-btn-run">\n  <i class="fa-solid fa-play"></i> Run Code\n</button>`
        },
        {
            name: "Secondary Action Button",
            html: `<button class="nx-btn"><i class="fa-solid fa-floppy-disk"></i> Save Snippet</button>`,
            code: `<button class="nx-btn">\n  <i class="fa-solid fa-floppy-disk"></i> Save Snippet\n</button>`
        },
        {
            name: "Danger Stop Button",
            html: `<button class="nx-btn nx-btn-stop"><i class="fa-solid fa-stop"></i> Stop Run</button>`,
            code: `<button class="nx-btn nx-btn-stop">\n  <i class="fa-solid fa-stop"></i> Stop Run\n</button>`
        },
        {
            name: "Glassmorphism Card",
            html: `<div style="background:var(--bg-card);border:1px solid var(--glass-border);padding:16px;border-radius:12px;backdrop-filter:blur(12px);width:100%"><h4 style="color:var(--primary);margin-bottom:6px;font-family:var(--font-accent);">System Console</h4><p style="font-size:12px;color:var(--text-dim);">Frosted glass container pattern.</p></div>`,
            code: `<div class="glass-card">\n  <h4>Title</h4>\n  <p>Content here</p>\n</div>`
        },
        {
            name: "Language Selector",
            html: `<select class="studio-select"><option>Python</option><option>C++</option><option>JavaScript</option></select>`,
            code: `<select class="studio-select">\n  <option>Language</option>\n</select>`
        },
        {
            name: "Toggle Switch",
            html: `<label class="switch"><input type="checkbox" checked><span class="slider"></span></label>`,
            code: `<label class="switch">\n  <input type="checkbox">\n  <span class="slider"></span>\n</label>`
        }
    ],

    flowDiagram: `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 860 520" width="100%" height="100%">
  <defs>
    <!-- Arrowhead marker -->
    <marker id="arrow-blue" viewBox="0 0 10 10" refX="7" refY="5"
            markerWidth="5" markerHeight="5" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#4f9eff"/>
    </marker>
    <marker id="arrow-green" viewBox="0 0 10 10" refX="7" refY="5"
            markerWidth="5" markerHeight="5" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#34d399"/>
    </marker>
    <marker id="arrow-violet" viewBox="0 0 10 10" refX="7" refY="5"
            markerWidth="5" markerHeight="5" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#a78bfa"/>
    </marker>
    <marker id="arrow-dim" viewBox="0 0 10 10" refX="7" refY="5"
            markerWidth="5" markerHeight="5" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="rgba(255,255,255,0.25)"/>
    </marker>
    <!-- Glow filter -->
    <filter id="glow-blue" x="-30%" y="-30%" width="160%" height="160%">
      <feGaussianBlur stdDeviation="3" result="blur"/>
      <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
    </filter>
    <filter id="glow-green" x="-30%" y="-30%" width="160%" height="160%">
      <feGaussianBlur stdDeviation="2.5" result="blur"/>
      <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
    </filter>
    <!-- Gradients -->
    <linearGradient id="grad-blue" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#4f9eff" stop-opacity="0.9"/>
      <stop offset="100%" stop-color="#1a5fd1" stop-opacity="0.7"/>
    </linearGradient>
    <linearGradient id="grad-card" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="rgba(255,255,255,0.05)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0.01)"/>
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="860" height="520" fill="#080c12" rx="12"/>
  <!-- Subtle grid dots -->
  <pattern id="dots" x="0" y="0" width="28" height="28" patternUnits="userSpaceOnUse">
    <circle cx="1" cy="1" r="1" fill="rgba(255,255,255,0.04)"/>
  </pattern>
  <rect width="860" height="520" fill="url(#dots)" rx="12"/>

  <!-- ─── BLOCKS ─────────────────────────────── -->

  <!-- 1. Landing Page -->
  <g transform="translate(30, 220)">
    <rect width="130" height="64" rx="10" fill="url(#grad-card)" stroke="#4f9eff" stroke-width="1.5" filter="url(#glow-blue)"/>
    <text x="65" y="26" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="12" font-weight="700" text-anchor="middle">Landing</text>
    <text x="65" y="42" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="12" font-weight="700" text-anchor="middle">Page</text>
    <text x="65" y="56" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">User Entry / Hero</text>
  </g>

  <!-- 2. Dashboard -->
  <g transform="translate(218, 220)">
    <rect width="130" height="64" rx="10" fill="url(#grad-card)" stroke="rgba(255,255,255,0.12)" stroke-width="1.2"/>
    <text x="65" y="26" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="12" font-weight="700" text-anchor="middle">Developer</text>
    <text x="65" y="42" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="12" font-weight="700" text-anchor="middle">Dashboard</text>
    <text x="65" y="56" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">Stats &amp; Projects</text>
  </g>

  <!-- 3. Compiler Workspace (Core) -->
  <g transform="translate(412, 214)">
    <rect width="160" height="76" rx="12" fill="rgba(79,158,255,0.07)" stroke="#4f9eff" stroke-width="2" filter="url(#glow-blue)"/>
    <text x="80" y="28" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="13" font-weight="800" text-anchor="middle">Compiler</text>
    <text x="80" y="46" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="13" font-weight="800" text-anchor="middle">Workspace</text>
    <text x="80" y="62" fill="#4f9eff" font-family="sans-serif" font-size="9" text-anchor="middle">Monaco Code Editor</text>
  </g>

  <!-- 4. User Profile -->
  <g transform="translate(218, 68)">
    <rect width="130" height="54" rx="9" fill="url(#grad-card)" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
    <text x="65" y="28" fill="#c8d3e0" font-family="'Outfit',sans-serif" font-size="11" font-weight="700" text-anchor="middle">User Profile</text>
    <text x="65" y="43" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">Stats &amp; Metrics</text>
  </g>

  <!-- 5. Settings -->
  <g transform="translate(218, 378)">
    <rect width="130" height="54" rx="9" fill="url(#grad-card)" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
    <text x="65" y="28" fill="#c8d3e0" font-family="'Outfit',sans-serif" font-size="11" font-weight="700" text-anchor="middle">Settings Page</text>
    <text x="65" y="43" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">Editor Config</text>
  </g>

  <!-- 6. AI Engine -->
  <g transform="translate(648, 82)">
    <rect width="140" height="56" rx="9" fill="rgba(167,139,250,0.08)" stroke="#a78bfa" stroke-width="1.5"/>
    <text x="70" y="25" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="11" font-weight="700" text-anchor="middle">AI Assistant</text>
    <text x="70" y="40" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">Explain · Fix · Optimize</text>
  </g>

  <!-- 7. Execution Sandbox -->
  <g transform="translate(648, 222)">
    <rect width="140" height="56" rx="9" fill="rgba(52,211,153,0.08)" stroke="#34d399" stroke-width="1.5" filter="url(#glow-green)"/>
    <text x="70" y="25" fill="#eef2f8" font-family="'Outfit',sans-serif" font-size="11" font-weight="700" text-anchor="middle">Execution Sandbox</text>
    <text x="70" y="40" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">Secure Compiler API</text>
  </g>

  <!-- 8. Share & Collab -->
  <g transform="translate(648, 358)">
    <rect width="140" height="56" rx="9" fill="url(#grad-card)" stroke="rgba(255,255,255,0.12)" stroke-width="1"/>
    <text x="70" y="25" fill="#c8d3e0" font-family="'Outfit',sans-serif" font-size="11" font-weight="700" text-anchor="middle">Share &amp; Collaborate</text>
    <text x="70" y="40" fill="#6b7a90" font-family="sans-serif" font-size="9" text-anchor="middle">Live Session Links</text>
  </g>

  <!-- ─── CONNECTIONS ───────────────────────── -->

  <!-- Landing → Dashboard -->
  <path d="M 160 252 L 212 252" fill="none" stroke="#4f9eff" stroke-width="2" marker-end="url(#arrow-blue)" stroke-dasharray="none"/>

  <!-- Dashboard ↔ Profile -->
  <path d="M 265 220 L 265 128" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow-dim)"/>
  <path d="M 282 128 L 282 220" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1.2" marker-end="url(#arrow-dim)"/>

  <!-- Dashboard ↔ Settings -->
  <path d="M 265 284 L 265 372" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow-dim)"/>
  <path d="M 282 372 L 282 284" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1.2" marker-end="url(#arrow-dim)"/>

  <!-- Dashboard → Workspace -->
  <path d="M 348 252 L 406 252" fill="none" stroke="#4f9eff" stroke-width="2" marker-end="url(#arrow-blue)"/>

  <!-- Workspace → AI Engine -->
  <path d="M 542 228 C 590 200, 600 148, 642 116" fill="none" stroke="#a78bfa" stroke-width="1.8" marker-end="url(#arrow-violet)"/>

  <!-- Workspace ↔ Execution Sandbox -->
  <path d="M 572 255 L 642 252" fill="none" stroke="#34d399" stroke-width="2" marker-end="url(#arrow-green)"/>
  <path d="M 642 265 C 602 290, 580 272, 572 266" fill="none" stroke="#34d399" stroke-width="1.5" marker-end="url(#arrow-green)"/>

  <!-- Workspace → Share -->
  <path d="M 542 278 C 590 318, 605 350, 642 372" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow-dim)"/>

  <!-- Labels on arrows -->
  <text x="182" y="246" fill="#4f9eff" font-family="sans-serif" font-size="8">navigate</text>
  <text x="360" y="246" fill="#4f9eff" font-family="sans-serif" font-size="8">open IDE</text>
  <text x="596" y="248" fill="#34d399" font-family="sans-serif" font-size="8">run</text>
</svg>
`
};
