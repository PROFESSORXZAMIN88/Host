<?php

$BOT_TOKEN = "8313070388:AAGcZKoeH67UwAv7Njn4V2Gfa43FMpkFWh0";

$LOG_FILE = __DIR__ . '/bot.log';

if (!file_exists($LOG_FILE)) {
    file_put_contents($LOG_FILE, "");
}

function logInfo($message) {
    global $LOG_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] INFO: $message\n";
    file_put_contents($LOG_FILE, $log_message, FILE_APPEND | LOCK_EX);
}

function logError($message) {
    global $LOG_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] ERROR: $message\n";
    file_put_contents($LOG_FILE, $log_message, FILE_APPEND | LOCK_EX);
}

function sendHTMLMessage($token, $chat_id, $text, $keyboard = null, $disable_link_preview = false) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $payload = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    if ($keyboard) {
        $payload['reply_markup'] = json_encode($keyboard);
    }
    if ($disable_link_preview) {
        $payload['disable_web_page_preview'] = true;
    }
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($payload)
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        logError("Failed to send message to chat_id $chat_id");
        return false;
    }
    $result = json_decode($response, true);
    if (!$result['ok']) {
        logError("Telegram API error for chat_id $chat_id: " . json_encode($result));
        return false;
    }
    logInfo("Successfully sent message to chat_id $chat_id");
    return true;
}

function sendPhoto($token, $chat_id, $photo, $caption, $keyboard = null) {
    $url = "https://api.telegram.org/bot$token/sendPhoto";
    $payload = [
        'chat_id' => $chat_id,
        'photo' => $photo,
        'caption' => $caption,
        'parse_mode' => 'HTML'
    ];
    if ($keyboard) {
        $payload['reply_markup'] = json_encode($keyboard);
    }
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($payload)
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        logError("Failed to send photo to chat_id $chat_id");
        return false;
    }
    $result = json_decode($response, true);
    if (!$result['ok']) {
        logError("Telegram API error when sending photo to chat_id $chat_id: " . json_encode($result));
        return false;
    }
    logInfo("Successfully sent photo to chat_id $chat_id");
    return true;
}

$menu_buttons = [
    'keyboard' => [
        [
            [
                'text' => '👤 User Info',
                'request_users' => [
                    'request_id' => 1,
                    'user_is_bot' => false,
                    'max_quantity' => 1,
                    'request_name' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ]
        ],
        [
            [
                'text' => '👥 Public Group',
                'request_chat' => [
                    'request_id' => 7,
                    'chat_is_channel' => false,
                    'chat_has_username' => true,
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ],
            [
                'text' => '🔒 Private Group',
                'request_chat' => [
                    'request_id' => 6,
                    'chat_is_channel' => false,
                    'chat_has_username' => false,
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ]
        ],
        [
            [
                'text' => '📢 Public Channel',
                'request_chat' => [
                    'request_id' => 5,
                    'chat_is_channel' => true,
                    'chat_has_username' => true,
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ],
            [
                'text' => '🔒 Private Channel',
                'request_chat' => [
                    'request_id' => 4,
                    'chat_is_channel' => true,
                    'chat_has_username' => false,
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ]
        ],
        [
            [
                'text' => '🤖 Bot',
                'request_users' => [
                    'request_id' => 2,
                    'user_is_bot' => true,
                    'max_quantity' => 1,
                    'request_name' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ],
            [
                'text' => '🌟 Premium Users',
                'request_users' => [
                    'request_id' => 3,
                    'user_is_premium' => true,
                    'max_quantity' => 1,
                    'request_name' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ]
        ],
        [
            ['text' => '👥 Admins Chat'],
            ['text' => '👑 Owner Chat']
        ]
    ],
    'resize_keyboard' => true,
    'input_field_placeholder' => 'Choose a chat type'
];

$my_buttons = [
    'keyboard' => [
        [
            [
                'text' => '📢 Your Channel',
                'request_chat' => [
                    'request_id' => 9,
                    'chat_is_channel' => true,
                    'user_admin_rights' => [
                        'can_manage_chat' => true,
                        'can_delete_messages' => true,
                        'can_manage_video_chats' => true,
                        'can_restrict_members' => true,
                        'can_promote_members' => true,
                        'can_change_info' => true,
                        'can_post_messages' => true,
                        'can_edit_messages' => true,
                        'can_invite_users' => true,
                        'can_pin_messages' => true,
                        'can_manage_topics' => true,
                        'can_post_stories' => true,
                        'can_edit_stories' => true,
                        'can_delete_stories' => true
                    ],
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ],
            [
                'text' => '👥 Your Group',
                'request_chat' => [
                    'request_id' => 8,
                    'chat_is_channel' => false,
                    'user_admin_rights' => [
                        'can_manage_chat' => true,
                        'can_delete_messages' => true,
                        'can_manage_video_chats' => true,
                        'can_restrict_members' => true,
                        'can_promote_members' => true,
                        'can_change_info' => true,
                        'can_invite_users' => true,
                        'can_pin_messages' => true,
                        'can_manage_topics' => true
                    ],
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ]
        ],
        [
            ['text' => '🔙 Back']
        ]
    ],
    'resize_keyboard' => true,
    'input_field_placeholder' => 'Choose a own chat type'
];

$admin_buttons = [
    'keyboard' => [
        [
            [
                'text' => '📢 Channels',
                'request_chat' => [
                    'request_id' => 10,
                    'chat_is_channel' => true,
                    'user_admin_rights' => [
                        'can_manage_chat' => true,
                        'can_delete_messages' => true,
                        'can_manage_video_chats' => true,
                        'can_restrict_members' => true,
                        'can_promote_members' => true,
                        'can_change_info' => true,
                        'can_post_messages' => true,
                        'can_edit_messages' => true,
                        'can_invite_users' => true,
                        'can_pin_messages' => true,
                        'can_manage_topics' => true,
                        'can_post_stories' => true,
                        'can_edit_stories' => true,
                        'can_delete_stories' => true
                    ],
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ],
            [
                'text' => '👥 Groups',
                'request_chat' => [
                    'request_id' => 11,
                    'chat_is_channel' => false,
                    'user_admin_rights' => [
                        'can_manage_chat' => true,
                        'can_delete_messages' => true,
                        'can_manage_video_chats' => true,
                        'can_restrict_members' => true,
                        'can_promote_members' => true,
                        'can_change_info' => true,
                        'can_invite_users' => true,
                        'can_pin_messages' => true,
                        'can_manage_topics' => true
                    ],
                    'request_title' => true,
                    'request_username' => true,
                    'request_photo' => true
                ]
            ]
        ],
        [
            ['text' => '🔙 Back']
        ]
    ],
    'resize_keyboard' => true,
    'input_field_placeholder' => 'Choose a admin chat type'
];

$types = [
    1 => ['name' => 'User'],
    2 => ['name' => 'Bot'],
    3 => ['name' => 'Premium User'],
    4 => ['name' => 'Private Channel'],
    5 => ['name' => 'Public Channel'],
    6 => ['name' => 'Private Group'],
    7 => ['name' => 'Public Group'],
    8 => ['name' => 'Your Group'],
    9 => ['name' => 'Your Channel'],
    10 => ['name' => 'Admin Channel'],
    11 => ['name' => 'Admin Group']
];

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update || !isset($update['message'])) {
    logError("No valid update or message received");
    exit;
}

logInfo("Received update: " . json_encode($update));

$message = $update['message'];
$chat_id = $message['chat']['id'];
$text = $message['text'] ?? '';

if ($text === '/start') {
    logInfo("Handling /start command for chat_id: $chat_id");
    $reply_text = "<b>👋 Welcome to Chat ID Finder Bot!</b> 🆔\n\n" .
                  "<b>✅ Fetch Any Chat ID Instantly!</b>\n\n" .
                  "🔧 <b>How to Use?</b>\n" .
                  "1️⃣ Click the buttons below to share a chat or user.\n" .
                  "2️⃣ Receive the unique ID instantly.\n\n" .
                  "<b>💎 Features:</b>\n" .
                  "- Supports users, bots, private/public groups & channels\n" .
                  "- Fast and reliable\n\n" .
                  "<blockquote>🛠 Made with ❤️ By @bizft</blockquote>";
    sendHTMLMessage($BOT_TOKEN, $chat_id, $reply_text, $menu_buttons, true);
    logInfo("Sent /start message with keyboard to chat_id: $chat_id");
}

if ($text === '/my') {
    logInfo("Handling /my command for chat_id: $chat_id");
    $reply_text = "<b>📚 Your Channels and Groups</b>\n\n" .
                  "🔧 <b>How to Use?</b>\n" .
                  "1️⃣ Click the buttons below to share your channel or group.\n" .
                  "2️⃣ Receive the unique ID instantly.\n\n" .
                  "<blockquote>🛠 Made with ❤️ By @bizft</blockquote>";
    sendHTMLMessage($BOT_TOKEN, $chat_id, $reply_text, $my_buttons, true);
    logInfo("Sent /my message with keyboard to chat_id: $chat_id");
}

if ($text === '/admin') {
    logInfo("Handling /admin command for chat_id: $chat_id");
    $reply_text = "<b>🛡️ Channels and Groups Where You Are Admin</b>\n\n" .
                  "🔧 <b>How to Use?</b>\n" .
                  "1️⃣ Click the buttons below to share a channel or group where you have admin privileges.\n" .
                  "2️⃣ Receive the unique ID instantly.\n\n" .
                  "<blockquote>🛠 Made with ❤️ By @bizft</blockquote>";
    sendHTMLMessage($BOT_TOKEN, $chat_id, $reply_text, $admin_buttons, true);
    logInfo("Sent /admin message with keyboard to chat_id: $chat_id");
}

if ($text === '👥 Admins Chat') {
    logInfo("Admins Chat button clicked for chat_id: $chat_id");
    $reply_text = "<b>🛡️ Channels and Groups Where You Are Admin</b>\n\n" .
                  "🔧 <b>How to Use?</b>\n" .
                  "1️⃣ Click the buttons below to share a channel or group where you have admin privileges.\n" .
                  "2️⃣ Receive the unique ID instantly.\n\n" .
                  "<blockquote>🛠 Made with ❤️ By @bizft</blockquote>";
    sendHTMLMessage($BOT_TOKEN, $chat_id, $reply_text, $admin_buttons, true);
    logInfo("Sent Admins Chat message with keyboard to chat_id: $chat_id");
}

if ($text === '👑 Owner Chat') {
    logInfo("Owner Chat button clicked for chat_id: $chat_id");
    $reply_text = "<b>📚 Your Channels and Groups</b>\n\n" .
                  "🔧 <b>How to Use?</b>\n" .
                  "1️⃣ Click the buttons below to share your channel or group.\n" .
                  "2️⃣ Receive the unique ID instantly.\n\n" .
                  "<blockquote>🛠 Made with ❤️ By @bizft</blockquote>";
    sendHTMLMessage($BOT_TOKEN, $chat_id, $reply_text, $my_buttons, true);
    logInfo("Sent Owner Chat message with keyboard to chat_id: $chat_id");
}

if ($text === '🔙 Back') {
    logInfo("Back button clicked for chat_id: $chat_id");
    $reply_text = "<b>👋 Welcome to Chat ID Finder Bot!</b> 🆔\n\n" .
                  "<b>✅ Fetch Any Chat ID Instantly!</b>\n\n" .
                  "🔧 <b>How to Use?</b>\n" .
                  "1️⃣ Click the buttons below to share a chat or user.\n" .
                  "2️⃣ Receive the unique ID instantly.\n\n" .
                  "<b>💎 Features:</b>\n" .
                  "- Supports users, bots, private/public groups & channels\n" .
                  "- Fast and reliable\n\n" .
                  "<blockquote>🛠 Made with ❤️ By @bizft</blockquote>";
    sendHTMLMessage($BOT_TOKEN, $chat_id, $reply_text, $menu_buttons, true);
    logInfo("Sent Back message with keyboard to chat_id: $chat_id");
}

if (isset($message['users_shared'])) {
    logInfo("Handling users_shared message");
    
    $request_id = $message['users_shared']['request_id'] ?? null;
    if (!$request_id || !isset($types[$request_id])) {
        logError("Invalid or missing request_id for users_shared");
        $response = "⚠️ <b>Error:</b> Invalid user type shared.";
        sendHTMLMessage($BOT_TOKEN, $chat_id, $response);
        exit;
    }
    
    $type = $types[$request_id]['name'];
    $users = $message['users_shared']['users'] ?? [];
    
    if (empty($users)) {
        logError("No users in users_shared for request_id $request_id");
        $response = "⚠️ <b>Error:</b> Unable to retrieve $type information.";
        sendHTMLMessage($BOT_TOKEN, $chat_id, $response);
        exit;
    }
    
    foreach ($users as $user) {
        $user_id = $user['user_id'] ?? 'Unknown';
        $first_name = $user['first_name'] ?? '';
        $last_name = $user['last_name'] ?? '';
        $username = isset($user['username']) ? "@{$user['username']}" : "No username";
        $full_name = trim("$first_name $last_name");
        
        logInfo("Processing shared user: ID=$user_id, Name=$full_name, Username=$username");
        
        $text = "<b>Shared $type Info</b>\n";
        $text .= "Type: <code>$type</code>\n";
        $text .= "ID: <code>$user_id</code>\n";
        $text .= "Name: <code>$full_name</code>\n";
        $text .= "Username: <code>$username</code>";
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => $full_name,
                        'copy_text' => [
                            'text' => (string)$user_id
                        ]
                    ]
                ]
            ]
        ];
        
        if (isset($user['photo']) && is_array($user['photo']) && !empty($user['photo'])) {
            $photos = $user['photo'];
            $photo_file_id = null;
            
            if (isset($photos[0]['file_id'])) {
                $photo_file_id = $photos[0]['file_id'];
            } elseif (isset($photos['small_file_id'])) {
                $photo_file_id = $photos['small_file_id'];
            }
            
            if ($photo_file_id) {
                logInfo("Sending user info with photo for user_id: $user_id");
                sendPhoto($BOT_TOKEN, $chat_id, $photo_file_id, $text, $keyboard);
            } else {
                logInfo("Sending user info without photo for user_id: $user_id");
                sendHTMLMessage($BOT_TOKEN, $chat_id, $text, $keyboard);
            }
        } else {
            logInfo("Sending user info without photo for user_id: $user_id");
            sendHTMLMessage($BOT_TOKEN, $chat_id, $text, $keyboard);
        }
    }
}

if (isset($message['chat_shared'])) {
    logInfo("Handling chat_shared message");
    
    $request_id = $message['chat_shared']['request_id'] ?? null;
    if (!$request_id || !isset($types[$request_id])) {
        logError("Invalid or missing request_id for chat_shared");
        $response = "⚠️ <b>Error:</b> Invalid chat type shared.";
        sendHTMLMessage($BOT_TOKEN, $chat_id, $response);
        exit;
    }
    
    $type = $types[$request_id]['name'];
    $shared_id = $message['chat_shared']['chat_id'] ?? 'Unknown';
    $title = $message['chat_shared']['title'] ?? 'Unnamed Chat';
    $username = isset($message['chat_shared']['username']) ? "@{$message['chat_shared']['username']}" : "No username";
    
    if ($shared_id === 'Unknown') {
        logError("Missing chat_id in chat_shared for request_id $request_id");
        $response = "⚠️ <b>Error:</b> Unable to retrieve $type ID.";
        sendHTMLMessage($BOT_TOKEN, $chat_id, $response);
        exit;
    }
    
    logInfo("Processing shared chat: ID=$shared_id, Title=$title, Username=$username");
    
    $text = "<b>Shared $type Info</b>\n";
    $text .= "Type: <code>$type</code>\n";
    $text .= "ID: <code>$shared_id</code>\n";
    $text .= "Name: <code>$title</code>\n";
    $text .= "Username: <code>$username</code>";
    
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => $title,
                    'copy_text' => [
                        'text' => (string)$shared_id
                    ]
                ]
            ]
        ]
    ];
    
    if (isset($message['chat_shared']['photo']) && is_array($message['chat_shared']['photo']) && !empty($message['chat_shared']['photo'])) {
        $photos = $message['chat_shared']['photo'];
        $photo_file_id = null;
        
        if (isset($photos[0]['file_id'])) {
            $photo_file_id = $photos[0]['file_id'];
        } elseif (isset($photos['small_file_id'])) {
            $photo_file_id = $photos['small_file_id'];
        }
        
        if ($photo_file_id) {
            logInfo("Sending chat info with photo for chat_id: $shared_id");
            sendPhoto($BOT_TOKEN, $chat_id, $photo_file_id, $text, $keyboard);
        } else {
            logInfo("Sending chat info without photo for chat_id: $shared_id");
            sendHTMLMessage($BOT_TOKEN, $chat_id, $text, $keyboard);
        }
    } else {
        logInfo("Sending chat info without photo for chat_id: $shared_id");
        sendHTMLMessage($BOT_TOKEN, $chat_id, $text, $keyboard);
    }
}

?>
