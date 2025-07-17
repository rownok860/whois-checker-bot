<h1>🔍 WHOIS Checker Telegram Bot</h1>

<p>
  <a href="https://t.me/WhoisCheckrRobot"><img src="https://img.shields.io/badge/TRY%20DEMO-%40WhoisCheckrRobot-blue?style=for-the-badge&amp;logo=telegram" alt="Telegram Demo"></a><br>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&amp;logo=php" alt="PHP Version"></a><br>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"></a>
</p>

<p>A feature-rich Telegram bot that performs instant WHOIS domain lookups with beautiful formatting and affiliate integration.</p>

<p><img src="https://i.ibb.co/BH1WYvyJ/Screenshot-2025-07-17-214208.png" alt="Demo Screenshot"></p>

<hr>

<h2>✨ Features</h2>

<ul>
  <li>🚀 Instant WHOIS domain lookups</li>
  <li>🎨 Beautifully formatted responses</li>
  <li>💰 Buy Domain/Hosting affiliate button</li>
  <li>📢 Join Channel promotion button</li>
  <li>👨‍💻 Admin dashboard with statistics</li>
  <li>📊 Search history logging</li>
 
</ul>

<hr>

<h2>🚀 Quick Demo</h2>

<p>Try the live demo:<br>
  <a href="https://t.me/WhoisCheckrRobot"><img src="https://img.shields.io/badge/TRY%20NOW-%40WhoisCheckrRobot-blue?style=for-the-badge&amp;logo=telegram" alt="Try Bot"></a>
</p>

<hr>

<h2>🛠 Setup Guide</h2>

<h3>📋 Requirements</h3>

<ul>
  <li>PHP 7.4+ with cURL extension</li>
  <li>Web server (Apache/Nginx)</li>
  <li>Telegram bot token</li>
  <li>APILayer WHOIS API key</li>
</ul>

<h3>🔧 Installation Steps</h3>

<ol>
  <li><strong>Get Bot Token</strong><br>Create your bot via <a href="https://t.me/BotFather">@BotFather</a> on Telegram.</li>
  <li><strong>Get API Key</strong><br>Register at <a href="https://apilayer.com/marketplace/whois-api">APILayer WHOIS API</a> for a free API key.</li>
  <li><strong>Configure Bot</strong><br>Edit <code>bot.php</code> with your credentials:</li>
</ol>

<pre><code class="language-php">// Main Configuration
define('BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE');
define('ADMIN_ID', 'YOUR_TELEGRAM_ID'); // For admin commands
define('AFFILIATE_URL', 'YOUR_AFFILIATE_LINK');
define('CHANNEL_URL', 'https://t.me/YOUR_CHANNEL');
define('WHOIS_API_KEY', 'YOUR_APILAYER_KEY');
</code></pre>

<ol start="4">
  <li><strong>Upload Files</strong><br>Deploy these files to your web server:<br>
    <ul>
      <li><code>bot.php</code> - Main bot script</li>
      <li><code>data.json</code> - Will be created automatically (ensure writable permissions)</li>
    </ul>
  </li>
  <li><strong>Set Webhook</strong><br>Activate the bot using this cURL command:</li>
</ol>

<pre><code class="language-bash">curl -X POST "https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://yourdomain.com/path/to/bot.php"
</code></pre>

<hr>

<h2>🎨 Sample Response Format</h2>

<pre><code>┌───────────────────────────────────────┐
│ 🟢 WHOIS Lookup Results                │
├───────────────────────────────────────┤
│ 🔹 Domain: example.com                 │
│ 🔹 Registrar: GoDaddy.com, LLC         │
│ 🔹 WHOIS Server: whois.godaddy.com     │
├───────────────┬───────────────────────┤
│ 📅 Created    │ 2020-01-01 12:00:00   │
│ 🔄 Updated    │ 2023-05-15 09:30:00   │
│ ⌛ Expires    │ 2025-01-01 12:00:00   │
├───────────────┴───────────────────────┤
│ 🔒 Status:                             │
│ • clientDeleteProhibited               │
│ • clientRenewProhibited                │
├───────────────────────────────────────┤
│ 🌐 Nameservers:                       │
│ • ns1.example.com                     │
│ • ns2.example.com                     │
└───────────────────────────────────────┘
</code></pre>

<hr>

<h2>👨‍💻 Admin Commands</h2>

<table>
  <thead>
    <tr>
      <th>Command</th>
      <th>Description</th>
      <th>Example</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>/stats</code></td>
      <td>Show user and search counts</td>
      <td><code>/stats</code></td>
    </tr>
    <tr>
      <td><code>/domains</code></td>
      <td>Show last 10 searches</td>
      <td><code>/domains</code></td>
    </tr>
    <tr>
      <td><code>/broadcast</code></td>
      <td>Send message to all users</td>
      <td><code>/broadcast Hello users!</code></td>
    </tr>
  </tbody>
</table>

<hr>

<h2>📁 File Structure</h2>

<pre><code>whois-bot/
├── bot.php             # Main bot script
├── data.json           # Auto-generated data file
├── README.md           # This documentation
└── LICENSE             # MIT License file
</code></pre>

<hr>

<h2>🤝 Contributing</h2>

<ol>
  <li>Fork the project</li>
  <li>Create your feature branch (<code>git checkout -b feature/AmazingFeature</code>)</li>
  <li>Commit your changes (<code>git commit -m 'Add some amazing feature'</code>)</li>
  <li>Push to the branch (<code>git push origin feature/AmazingFeature</code>)</li>
  <li>Open a Pull Request</li>
</ol>

<hr>

<h2>📜 License</h2>

<p>Distributed under the MIT License. See <code>LICENSE</code> for more information.</p>

<hr>

<h2>📬 Contact</h2>

<p>For questions or support:<br>
email: hello@rownok.com</p>
