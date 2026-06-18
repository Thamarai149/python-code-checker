// Nexus Studio Design System & Component Library Config
const DesignSystem = {
    theme: {
        colors: [
            { name: "Background Deep", value: "#0D1117", description: "Primary application backdrop" },
            { name: "Primary Accent", value: "#58A6FF", description: "Buttons, links, and active indicators" },
            { name: "Secondary Action", value: "#1F6FEB", description: "Secondary actions and subtle alerts" },
            { name: "Success Green", value: "#3FB950", description: "Successful compiler runs and green states" },
            { name: "Error Red", value: "#F85149", description: "Compilation failures and critical alerts" },
            { name: "Text Main", value: "#C9D1D9", description: "High-contrast primary readable content" },
            { name: "Border Glass", value: "rgba(255, 255, 255, 0.08)", description: "Glassmorphism panels and borders" },
            { name: "Console Dark", value: "#05070a", description: "Terminal backdrop" }
        ],
        typography: {
            fonts: [
                { name: "Primary Font", family: "'Inter', sans-serif", usage: "Main interfaces, dashboards, inputs" },
                { name: "Accent Font", family: "'Outfit', sans-serif", usage: "Titles, headers, brand elements" },
                { name: "Monospace Font", family: "'Fira Code', 'Cascadia Code', monospace", usage: "Code editor and output terminal" }
            ]
        }
    },
    components: [
        {
            name: "Primary Run Button",
            html: `<button class="nx-btn nx-btn-run"><i class="fa-solid fa-play"></i> Run Code</button>`,
            code: `<button class="nx-btn nx-btn-run"><i class="fa-solid fa-play"></i> Run Code</button>`
        },
        {
            name: "Secondary Save Button",
            html: `<button class="nx-btn"><i class="fa-solid fa-floppy-disk"></i> Save Snippet</button>`,
            code: `<button class="nx-btn"><i class="fa-solid fa-floppy-disk"></i> Save Snippet</button>`
        },
        {
            name: "Danger Stop Button",
            html: `<button class="nx-btn nx-btn-stop"><i class="fa-solid fa-stop"></i> Stop Run</button>`,
            code: `<button class="nx-btn nx-btn-stop"><i class="fa-solid fa-stop"></i> Stop Run</button>`
        },
        {
            name: "Glassmorphism Card",
            html: `<div style="background:var(--bg-card); border:1px solid var(--glass-border); padding:15px; border-radius:10px; backdrop-filter:blur(10px); width:100%"><h4 style="color:var(--neon-blue); margin-bottom:5px;">System Console</h4><p style="font-size:12px; color:var(--text-dim);">Frosted glass container pattern.</p></div>`,
            code: `<div class="glass-card">\n  <h4>Title</h4>\n  <p>Content</p>\n</div>`
        },
        {
            name: "Language Dropdown Selector",
            html: `<select class="studio-select"><option>Python</option><option>C++</option><option>JavaScript</option></select>`,
            code: `<select class="studio-select">\n  <option>Language</option>\n</select>`
        }
    ],
    flowDiagram: `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" width="100%" height="100%">
  <!-- Definitions for Arrowheads -->
  <defs>
    <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#58A6FF" />
    </marker>
    <linearGradient id="neonGlow" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#58A6FF" />
      <stop offset="100%" stop-color="#1F6FEB" />
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="100%" height="100%" fill="#0a0b10" rx="15" />

  <!-- Flow Blocks -->
  <!-- Block 1: Landing Page -->
  <g transform="translate(40, 210)">
    <rect width="120" height="60" rx="10" fill="rgba(88, 166, 255, 0.1)" stroke="#58A6FF" stroke-width="2" />
    <text x="60" y="35" fill="#C9D1D9" font-family="'Outfit', sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Landing Page</text>
    <text x="60" y="48" fill="#7D8590" font-family="sans-serif" font-size="9" text-anchor="middle">User Entry / Hero</text>
  </g>

  <!-- Block 2: Dashboard -->
  <g transform="translate(220, 210)">
    <rect width="120" height="60" rx="10" fill="rgba(255, 255, 255, 0.03)" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
    <text x="60" y="30" fill="#C9D1D9" font-family="'Outfit', sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Developer</text>
    <text x="60" y="45" fill="#C9D1D9" font-family="'Outfit', sans-serif" font-size="12" font-weight="bold" text-anchor="middle">Dashboard</text>
  </g>

  <!-- Block 3: Compiler Workspace (Center Core) -->
  <g transform="translate(420, 210)">
    <rect width="160" height="70" rx="12" fill="rgba(0, 242, 255, 0.08)" stroke="#00f2ff" stroke-width="2" filter="drop-shadow(0 0 10px rgba(0,242,255,0.2))" />
    <text x="80" y="35" fill="#e6edf3" font-family="'Outfit', sans-serif" font-size="14" font-weight="bold" text-anchor="middle">Compiler Workspace</text>
    <text x="80" y="52" fill="#7d8590" font-family="sans-serif" font-size="10" text-anchor="middle">Monaco Code Editor</text>
  </g>

  <!-- Branch Block 4: User Profile -->
  <g transform="translate(220, 70)">
    <rect width="120" height="50" rx="8" fill="rgba(255, 255, 255, 0.03)" stroke="rgba(255,255,255,0.1)" stroke-width="1" />
    <text x="60" y="30" fill="#C9D1D9" font-family="'Outfit', sans-serif" font-size="11" text-anchor="middle">User Profile</text>
  </g>

  <!-- Branch Block 5: Settings Page -->
  <g transform="translate(220, 350)">
    <rect width="120" height="50" rx="8" fill="rgba(255, 255, 255, 0.03)" stroke="rgba(255,255,255,0.1)" stroke-width="1" />
    <text x="60" y="30" fill="#C9D1D9" font-family="'Outfit', sans-serif" font-size="11" text-anchor="middle">Settings Page</text>
  </g>

  <!-- Branch Block 6: AI Engine -->
  <g transform="translate(640, 90)">
    <rect width="120" height="50" rx="8" fill="rgba(157, 0, 255, 0.1)" stroke="#9d00ff" stroke-width="1.5" />
    <text x="60" y="25" fill="#e6edf3" font-family="'Outfit', sans-serif" font-size="11" font-weight="bold" text-anchor="middle">AI Assistant</text>
    <text x="60" y="38" fill="#7d8590" font-family="sans-serif" font-size="9" text-anchor="middle">Optimize & Refactor</text>
  </g>

  <!-- Branch Block 7: Execution Sandbox -->
  <g transform="translate(640, 220)">
    <rect width="120" height="50" rx="8" fill="rgba(63, 185, 80, 0.1)" stroke="#3FB950" stroke-width="1.5" />
    <text x="60" y="25" fill="#e6edf3" font-family="'Outfit', sans-serif" font-size="11" font-weight="bold" text-anchor="middle">Execution Box</text>
    <text x="60" y="38" fill="#7d8590" font-family="sans-serif" font-size="9" text-anchor="middle">Secure Compiler</text>
  </g>

  <!-- Branch Block 8: Sharing & Collab -->
  <g transform="translate(640, 340)">
    <rect width="120" height="50" rx="8" fill="rgba(255, 255, 255, 0.03)" stroke="rgba(255,255,255,0.1)" stroke-width="1" />
    <text x="60" y="30" fill="#C9D1D9" font-family="'Outfit', sans-serif" font-size="11" text-anchor="middle">Share & Collaborate</text>
  </g>

  <!-- CONNECTIONS / ARROWS -->
  <!-- Landing -> Dashboard -->
  <path d="M 160 240 L 214 240" fill="none" stroke="#58A6FF" stroke-width="2" marker-end="url(#arrow)" />
  
  <!-- Dashboard -> Profile -->
  <path d="M 280 210 L 280 126" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow)" />
  <path d="M 280 120 L 280 204" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow)" />

  <!-- Dashboard -> Settings -->
  <path d="M 280 270 L 280 344" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow)" />
  <path d="M 280 350 L 280 276" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow)" />

  <!-- Dashboard -> Workspace -->
  <path d="M 340 240 L 414 240" fill="none" stroke="#58A6FF" stroke-width="2" marker-end="url(#arrow)" />

  <!-- Workspace -> AI Engine -->
  <path d="M 540 210 C 580 180, 580 130, 634 118" fill="none" stroke="#9d00ff" stroke-width="1.5" marker-end="url(#arrow)" />

  <!-- Workspace -> Execution Sandbox (Core loop) -->
  <path d="M 580 245 L 634 245" fill="none" stroke="#3FB950" stroke-width="2" marker-end="url(#arrow)" />
  <path d="M 640 260 C 600 280, 590 270, 580 260" fill="none" stroke="#3FB950" stroke-width="1.5" marker-end="url(#arrow)" />

  <!-- Workspace -> Share -->
  <path d="M 540 280 C 580 310, 580 350, 634 360" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" marker-end="url(#arrow)" />
</svg>
`
};
