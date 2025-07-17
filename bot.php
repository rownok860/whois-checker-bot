<?php
// Configuration
define('BOT_TOKEN', 'ABCD:122344');  // Replace with your bot token
define('ADMIN_ID', 1234567);  // Replace with your telegram id
define('AFFILIATE_URL', 'https://hostinger.com?REFERRALCODE=rownok');
define('CHANNEL_URL', 'https://t.me/hypercircletech'); // Replace with your channel URL
define('DATA_FILE', 'data.json');
define('WHOIS_API_URL', 'https://api.apilayer.com/whois/query?domain=');
define('WHOIS_API_KEY', 'ggsggggg'); // Replace with Apilayer's api

// Initialize data file if it doesn't exist
if (!file_exists(DATA_FILE)) {
    file_put_contents(DATA_FILE, json_encode(['users' => [], 'searches' => []]));
}

// Get input from Telegram webhook
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Process the update
if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'];
    $userId = $message['from']['id'];
    $text = $message['text'] ?? '';
    
    // Track user if not already in database
    trackUser($userId, $message['from']);
    
    // Check for /start command
    if ($text === '/start') {
        sendWelcomeMessage($chatId);
        return;
    }
    
    // Check for admin commands
    if ($userId == ADMIN_ID && strpos($text, '/') === 0) {
        handleAdminCommand($text, $chatId);
        return;
    }
    
    // Handle domain lookup
    if (isValidDomain($text)) {
        $domain = strtolower(trim($text));
        $whoisData = performWhoisLookup($domain);
        
        // Log the search
        logSearch($userId, $domain);
        
        // Send response with button
        sendWhoisResponse($chatId, $domain, $whoisData);
    } else {
        sendMessage($chatId, "Please send a valid domain name (e.g., google.com) to perform a WHOIS lookup.");
    }
}

// Functions
function sendWelcomeMessage($chatId) {
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => 'Join Channel',
                    'url' => CHANNEL_URL
                ]
            ]
        ]
    ];
    
    $encodedKeyboard = json_encode($keyboard);
    
    $params = [
        'chat_id' => $chatId,
        'text' => "👋 *I'm WHOIS Checker Bot*\n\nSend me a domain name to check WHOIS information.\nExample: `google.com`",
        'parse_mode' => 'Markdown',
        'reply_markup' => $encodedKeyboard
    ];
    
    apiRequest('sendMessage', $params);
}

function trackUser($userId, $userData) {
    $data = json_decode(file_get_contents(DATA_FILE), true);
    
    // Initialize users array if not exists
    if (!isset($data['users'])) {
        $data['users'] = [];
    }
    
    // Check if user already exists
    $userExists = false;
    foreach ($data['users'] as $user) {
        if ($user['id'] == $userId) {
            $userExists = true;
            break;
        }
    }
    
    if (!$userExists) {
        $data['users'][] = [
            'id' => $userId,
            'username' => $userData['username'] ?? null,
            'first_name' => $userData['first_name'] ?? null,
            'last_name' => $userData['last_name'] ?? null,
            'joined_at' => time()
        ];
        file_put_contents(DATA_FILE, json_encode($data));
    }
}

function logSearch($userId, $domain) {
    $data = json_decode(file_get_contents(DATA_FILE), true);
    
    // Initialize searches array if not exists
    if (!isset($data['searches'])) {
        $data['searches'] = [];
    }
    
    $data['searches'][] = [
        'user_id' => $userId,
        'domain' => $domain,
        'timestamp' => time()
    ];
    file_put_contents(DATA_FILE, json_encode($data));
}

function isValidDomain($domain) {
    $domain = strtolower(trim($domain));
    return preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/', $domain);
}

function performWhoisLookup($domain) {
    $url = WHOIS_API_URL . urlencode($domain);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . WHOIS_API_KEY
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return "Error: Could not retrieve WHOIS data (HTTP $httpCode)";
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        return "Error: " . ($data['error']['info'] ?? 'Unknown error');
    }

    if (!isset($data['result'])) {
        return "No WHOIS data found for $domain";
    }
    
    $result = $data['result'];
    
    // Format the WHOIS data
    $output = "┌───────────────────────────────────────┐\n";
    $output .= "│ 🟢 WHOIS Lookup Results\n";
    $output .= "├───────────────────────────────────────┤\n";
    $output .= "│ 🔹 Domain: " . ($result['domain_name'] ?? $domain) . "\n";
    $output .= "│ 🔹 Registrar: " . ($result['registrar'] ?? 'N/A') . "\n";
    $output .= "│ 🔹 WHOIS Server: " . ($result['whois_server'] ?? 'N/A') . "\n";
    $output .= "├───────────────┬───────────────────────┤\n";
    $output .= "│ 📅 Created    │ " . ($result['creation_date'] ?? 'N/A') . "\n";
    $output .= "│ 🔄 Updated    │ " . ($result['updated_date'] ?? 'N/A') . "\n";
    $output .= "│ ⌛ Expires    │ " . ($result['expiration_date'] ?? 'N/A') . "\n";
    $output .= "├───────────────┴───────────────────────┤\n";
    
    // Format status
    if (isset($result['status']) && is_array($result['status'])) {
        $output .= "│ 🔒 Status:\n";
        foreach ($result['status'] as $status) {
            $output .= "│ • " . preg_replace('/\s+https?:\/\/.*$/', '', $status) . "\n";
        }
    }
    
    // Format nameservers
    if (isset($result['name_servers']) && is_array($result['name_servers'])) {
        $output .= "├───────────────────────────────────────┤\n";
        $output .= "│ 🌐 Nameservers:\n";
        foreach ($result['name_servers'] as $ns) {
            $output .= "│ • " . $ns . "\n";
        }
    }
    
    // Add contact information if available
    $contactFields = [
        'org' => 'Organization',
        'name' => 'Registrant Name',
        'emails' => 'Email',
        'address' => 'Address',
        'city' => 'City',
        'state' => 'State',
        'country' => 'Country',
        'zipcode' => 'ZIP Code'
    ];
    
    $hasContactInfo = false;
    foreach ($contactFields as $field => $label) {
        if (!empty($result[$field])) {
            $hasContactInfo = true;
            break;
        }
    }
    
    if ($hasContactInfo) {
        $output .= "├───────────────────────────────────────┤\n";
        $output .= "│ 📇 Contact Information:\n";
        foreach ($contactFields as $field => $label) {
            if (!empty($result[$field])) {
                $output .= "│ • $label: " . $result[$field] . "\n";
            }
        }
    }
    
    $output .= "└───────────────────────────────────────┘\n";
    $output .= "🔎 DNSSEC: " . ($result['dnssec'] ?? 'Not specified');
    
    return substr($output, 0, 4000);
}

function sendWhoisResponse($chatId, $domain, $whoisData) {
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => '🛒 Buy Domain/Hosting',
                    'url' => AFFILIATE_URL
                ],
                [
                    'text' => 'Join Channel',
                    'url' => CHANNEL_URL
                ]
            ]
        ]
    ];
    
    $encodedKeyboard = json_encode($keyboard);
    
    $params = [
        'chat_id' => $chatId,
        'text' => "WHOIS for *$domain*:\n```\n$whoisData\n```",
        'parse_mode' => 'Markdown',
        'reply_markup' => $encodedKeyboard
    ];
    
    apiRequest('sendMessage', $params);
}

function sendMessage($chatId, $text) {
    $params = [
        'chat_id' => $chatId,
        'text' => $text
    ];
    
    apiRequest('sendMessage', $params);
}

function handleAdminCommand($command, $chatId) {
    $data = json_decode(file_get_contents(DATA_FILE), true);
    
    if (strpos($command, '/stats') === 0) {
        $userCount = count($data['users'] ?? []);
        $searchCount = count($data['searches'] ?? []);
        sendMessage($chatId, "📊 Stats:\nUsers: $userCount\nSearches: $searchCount");
    } 
    elseif (strpos($command, '/domains') === 0) {
        $searches = $data['searches'] ?? [];
        $lastSearches = array_slice($searches, -10, 10, true);
        $response = "🔍 Last 10 domain searches:\n";
        foreach (array_reverse($lastSearches) as $search) {
            $response .= sprintf(
                "%s - %s (User: %s)\n",
                date('Y-m-d H:i:s', $search['timestamp']),
                $search['domain'],
                $search['user_id']
            );
        }
        sendMessage($chatId, $response);
    }
    elseif (strpos($command, '/broadcast') === 0) {
        $message = trim(substr($command, strlen('/broadcast')));
        if (empty($message)) {
            sendMessage($chatId, "Usage: /broadcast <message>");
            return;
        }
        
        $success = 0;
        $failed = 0;
        foreach (($data['users'] ?? []) as $user) {
            try {
                sendMessage($user['id'], $message);
                $success++;
            } catch (Exception $e) {
                $failed++;
            }
        }
        sendMessage($chatId, "Broadcast complete:\nSuccess: $success\nFailed: $failed");
    }
    else {
        sendMessage($chatId, "Unknown admin command.");
    }
}

function apiRequest($method, $params = []) {
    $url = 'https://api.telegram.org/bot' . BOT_TOKEN . '/' . $method;
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>
