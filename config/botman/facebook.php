<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Facebook Token
    |--------------------------------------------------------------------------
    |
    | Your Facebook application you received after creating
    | the messenger page / application on Facebook.
    |
    */
    'token' => env('FACEBOOK_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Facebook App Secret
    |--------------------------------------------------------------------------
    |
    | Your Facebook application secret, which is used to verify
    | incoming requests from Facebook.
    |
    */
    'app_secret' => env('FACEBOOK_APP_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Facebook Verification
    |--------------------------------------------------------------------------
    |
    | Your Facebook verification token, used to validate the webhooks.
    |
    */
    'verification' => env('FACEBOOK_VERIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Facebook Start Button Payload
    |--------------------------------------------------------------------------
    |
    | The payload which is sent when the Get Started Button is clicked.
    |
    */
    'start_button_payload' => 'GET_STARTED',

    /*
    |--------------------------------------------------------------------------
    | Facebook Greeting Text
    |--------------------------------------------------------------------------
    |
    | Your Facebook Greeting Text which will be shown on your message start screen.
    |
    */
    'greeting_text' => [
        'greeting' => [
            [
                'locale' => 'default',
                'text' => 'Welcome!',
            ],
            [
                'locale' => 'en_US',
                'text' => 'An HIV Self testing Chatbot that provides frequently asked question, where to buy test kits, 
                How to use the test kits, and how to contact a counselor.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Facebook Persistent Menu
    |--------------------------------------------------------------------------
    |
    | Example items for your persistent Facebook menu.
    |
    */
    'persistent_menu' => [
        [
            'locale' => 'default',
            'composer_input_disabled' => 'false',
            'call_to_actions' => [
                [
                    'title' => 'View FAQs',
                    'type' => 'postback',
                    'payload' => 'faqs_1',
                ],
                [
                    'title' => 'Instructions',
                    'type' => 'postback',
                    'payload' => 'instructions_2',
                ],
                [
                    'title' => 'Find Locations',
                    'type' => 'postback',
                    'payload' => 'locations_3',
                ],
                [
                    'title' => 'Contact a counselor',
                    'type' => 'postback',
                    'payload' => 'counselors_4',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Facebook Domain Whitelist
    |--------------------------------------------------------------------------
    |
    | In order to use domains you need to whitelist them
    |
    */
    'whitelisted_domains' => [
        'https://developers.tmcg.co.ug',
    ],
];
