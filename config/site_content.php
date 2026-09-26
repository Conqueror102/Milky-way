<?php

/*
|--------------------------------------------------------------------------
| Editable Site Content
|--------------------------------------------------------------------------
|
| Every photo and piece of copy on the home page that admins can change from
| /admin/site, grouped by section in the order they appear on the page. The
| values here are the defaults: the site shows them until an admin saves
| something else, and "Restore default" brings them back.
|
| Field types:
|   text      one line
|   textarea  a paragraph
|   lines     a list, one item per line
|   image     a photo; `default` is the bundled photo (used for the admin
|             thumbnail), `alt` the default description of it
|
| `optional` fields may be saved empty; any other field left empty falls back
| to its default. Views read these through App\Support\SiteContent, as $site.
|
*/

return [

    'sections' => [

        'hero' => [
            'label' => 'Hero',
            'description' => 'The first screen visitors see, with the rotating photos.',
            'anchor' => 'top',
            'groups' => [
                [
                    'label' => 'Carousel photos',
                    'description' => 'These fade into each other behind the headline, in this order.',
                    'fields' => [
                        'slide_1' => ['type' => 'image', 'label' => 'Photo 1', 'default' => '/images/hero/slide-1.jpg', 'alt' => 'A woman smiling while applying raw shea butter to her face'],
                        'slide_2' => ['type' => 'image', 'label' => 'Photo 2', 'default' => '/images/hero/slide-2.jpg', 'alt' => 'A woman checking a hand mirror while applying skincare cream'],
                        'slide_3' => ['type' => 'image', 'label' => 'Photo 3', 'default' => '/images/hero/slide-4.jpg', 'alt' => 'Close-up of a woman applying a dollop of cream to her cheek'],
                    ],
                ],
                [
                    'label' => 'Headline',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small line above the headline', 'default' => 'Health/Beauty Supplements · Skincare Products · Body Enhancer'],
                        'heading' => ['type' => 'text', 'label' => 'First line', 'default' => 'Your Beauty.'],
                        'heading_second' => ['type' => 'text', 'label' => 'Second line', 'default' => 'Our'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'Passion', 'help' => 'Shown in the orange handwritten style at the end of the second line.'],
                        'intro' => ['type' => 'textarea', 'label' => 'Supporting text', 'default' => "Skincare, beauty, body enhancement and spa products at **wholesale and retail prices**. Shopping for yourself, or stocking your beauty business — we've got you covered.", 'help' => 'Put **double stars** around words to make them bold.'],
                    ],
                ],
                [
                    'label' => 'Buttons and badges',
                    'fields' => [
                        'primary_button' => ['type' => 'text', 'label' => 'Main button', 'default' => 'Shop our products'],
                        'secondary_button' => ['type' => 'text', 'label' => 'WhatsApp button', 'default' => 'Chat with us on WhatsApp'],
                        'badge_1' => ['type' => 'text', 'label' => 'Badge 1', 'default' => 'Wholesale & Retail'],
                        'badge_2' => ['type' => 'text', 'label' => 'Badge 2', 'default' => 'Delivery Available'],
                        'badge_3' => ['type' => 'text', 'label' => 'Badge 3', 'default' => 'Open 24 Hours'],
                    ],
                ],
            ],
        ],

        'categories' => [
            'label' => 'Shop by category',
            'description' => 'The category cards, the rotating feature photos and the category blurbs.',
            'anchor' => 'shop',
            'groups' => [
                [
                    'label' => 'Heading',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'Shop by category'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'One store, every'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'beauty'],
                        'heading_end' => ['type' => 'text', 'label' => 'Heading end', 'default' => 'need', 'optional' => true],
                    ],
                ],
                [
                    'label' => 'Feature photos',
                    'description' => 'The large panel beside the cards cycles through these.',
                    'fields' => [
                        'feature_1' => ['type' => 'image', 'label' => 'Feature photo 1', 'default' => '/images/showcase/applying-product.jpg', 'alt' => 'A woman applying a skincare product at her dressing table'],
                        'feature_2' => ['type' => 'image', 'label' => 'Feature photo 2', 'default' => '/images/showcase/face-roller.jpg', 'alt' => 'A woman using a rose quartz face roller'],
                        'feature_3' => ['type' => 'image', 'label' => 'Feature photo 3', 'default' => '/images/showcase/podium.jpg', 'alt' => 'Cosmetic bottles and tubes arranged on a display podium'],
                        'feature_4' => ['type' => 'image', 'label' => 'Feature photo 4', 'default' => '/images/showcase/spa-massage.jpg', 'alt' => 'A woman receiving an oil massage in a spa'],
                        'feature_button' => ['type' => 'text', 'label' => 'Feature panel button', 'default' => 'Enquire about stock'],
                    ],
                ],
                [
                    'label' => 'Category cards',
                    'description' => 'One card leads each category, ahead of its products.',
                    'fields' => [
                        'card_skincare' => ['type' => 'image', 'label' => 'Skincare card photo', 'default' => '/images/categories/skincare.jpg', 'alt' => 'Skincare'],
                        'card_skincare_name' => ['type' => 'text', 'label' => 'Skincare card title', 'default' => 'Skincare'],
                        'card_beauty' => ['type' => 'image', 'label' => 'Beauty & Cosmetics card photo', 'default' => '/images/categories/beauty.jpg', 'alt' => 'Beauty & Cosmetics'],
                        'card_beauty_name' => ['type' => 'text', 'label' => 'Beauty & Cosmetics card title', 'default' => 'Beauty & Cosmetics'],
                        'card_body' => ['type' => 'image', 'label' => 'Body Enhancement card photo', 'default' => '/images/categories/body.jpg', 'alt' => 'Body Enhancement'],
                        'card_body_name' => ['type' => 'text', 'label' => 'Body Enhancement card title', 'default' => 'Body Enhancement'],
                        'card_spa' => ['type' => 'image', 'label' => 'Spa & Massage card photo', 'default' => '/images/categories/spa.jpg', 'alt' => 'Spa & Massage'],
                        'card_spa_name' => ['type' => 'text', 'label' => 'Spa & Massage card title', 'default' => 'Spa & Massage'],
                        'card_wholesale' => ['type' => 'image', 'label' => 'Wholesale card photo', 'default' => '/images/categories/wholesale.jpg', 'alt' => 'Wholesale'],
                        'card_wholesale_name' => ['type' => 'text', 'label' => 'Wholesale card title', 'default' => 'Wholesale'],
                        'card_badge' => ['type' => 'text', 'label' => 'Badge on each card photo', 'default' => 'Retail · Bulk'],
                        'card_button' => ['type' => 'text', 'label' => 'Category card button', 'default' => 'View products'],
                        'product_button' => ['type' => 'text', 'label' => 'Product card button', 'default' => 'View product'],
                    ],
                ],
                [
                    'label' => 'Category blurbs',
                    'description' => 'Shown on the category card and in the feature panel when a category is picked.',
                    'fields' => [
                        'blurb_all' => ['type' => 'textarea', 'label' => 'All', 'default' => 'Everything we stock, in one place.'],
                        'blurb_skincare' => ['type' => 'textarea', 'label' => 'Skincare', 'default' => 'Cleansers, moisturisers, creams, serums, soaps and scrubs for every routine.'],
                        'blurb_beauty' => ['type' => 'textarea', 'label' => 'Beauty & Cosmetics', 'default' => 'Makeup, beauty essentials, accessories and the tools to apply them.'],
                        'blurb_health' => ['type' => 'textarea', 'label' => 'Health & Beauty', 'default' => 'Selected health and personal-care products to sit alongside your beauty shelf.'],
                        'blurb_sexual' => ['type' => 'textarea', 'label' => 'Sexual Enhancement', 'default' => 'Products to support intimacy and libido, for individuals and couples.'],
                        'blurb_body' => ['type' => 'textarea', 'label' => 'Body Enhancement', 'default' => 'Body-enhancement and personal-care products for a complete regimen.'],
                        'blurb_spa' => ['type' => 'textarea', 'label' => 'Spa & Massage', 'default' => 'Products and essentials for spas, massage businesses and professionals.'],
                        'blurb_wholesale' => ['type' => 'textarea', 'label' => 'Wholesale', 'default' => 'Bulk purchasing for retailers, resellers, salons, spas and beauty businesses.'],
                    ],
                ],
            ],
        ],

        'why' => [
            'label' => 'Why choose us',
            'description' => 'The six reasons to shop here, beside three photos.',
            'anchor' => 'why',
            'groups' => [
                [
                    'label' => 'Photos',
                    'description' => 'Stacked beside the reasons on a computer, in a row above them on a phone.',
                    'fields' => [
                        'photo_1' => ['type' => 'image', 'label' => 'Top photo', 'default' => '/images/showcase/face-roller.jpg', 'alt' => 'A woman using a rose quartz face roller'],
                        'photo_2' => ['type' => 'image', 'label' => 'Middle photo', 'default' => '/images/hero/slide-1.jpg', 'alt' => 'A woman smiling while applying raw shea butter to her face'],
                        'photo_3' => ['type' => 'image', 'label' => 'Bottom photo', 'default' => '/images/hero/slide-2.jpg', 'alt' => 'A woman checking a hand mirror while applying skincare cream'],
                    ],
                ],
                [
                    'label' => 'Heading',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'Why choose us'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'Quality beauty, made'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'accessible'],
                        'intro' => ['type' => 'textarea', 'label' => 'Intro', 'default' => 'For everyday shoppers and beauty businesses alike — here is what you can count on.'],
                    ],
                ],
                [
                    'label' => 'Reasons',
                    'fields' => [
                        'reason_1_title' => ['type' => 'text', 'label' => 'Reason 1 title', 'default' => 'Guaranteed Quality'],
                        'reason_1_text' => ['type' => 'textarea', 'label' => 'Reason 1 text', 'default' => '100% authentic skincare, beauty, and spa formulations.'],
                        'reason_2_title' => ['type' => 'text', 'label' => 'Reason 2 title', 'default' => 'Wholesale & Retail'],
                        'reason_2_text' => ['type' => 'textarea', 'label' => 'Reason 2 text', 'default' => 'Buy single units for yourself or bulk cartons for your business.'],
                        'reason_3_title' => ['type' => 'text', 'label' => 'Reason 3 title', 'default' => 'Wide Variety'],
                        'reason_3_text' => ['type' => 'textarea', 'label' => 'Reason 3 text', 'default' => 'Everything from daily cleansers and makeup to professional spa oils and wellness.'],
                        'reason_4_title' => ['type' => 'text', 'label' => 'Reason 4 title', 'default' => 'Prime Location'],
                        'reason_4_text' => ['type' => 'textarea', 'label' => 'Reason 4 text', 'default' => 'Easily accessible at Bornu Plaza, Tradefair Complex, Lagos.'],
                        'reason_5_title' => ['type' => 'text', 'label' => 'Reason 5 title', 'default' => '24/7 Availability'],
                        'reason_5_text' => ['type' => 'textarea', 'label' => 'Reason 5 text', 'default' => 'Order, enquire, and check stock anytime via WhatsApp.'],
                        'reason_6_title' => ['type' => 'text', 'label' => 'Reason 6 title', 'default' => 'Dedicated Customer Support'],
                        'reason_6_text' => ['type' => 'textarea', 'label' => 'Reason 6 text', 'default' => 'Fast responses, product guidance, and reliable service.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Chat with us'],
                    ],
                ],
            ],
        ],

        'audience' => [
            'label' => 'Who we serve',
            'description' => 'The artwork of the people who shop here, with a label on each.',
            'anchor' => 'who',
            'groups' => [
                [
                    'label' => 'Heading',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'Who we serve'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'Who can shop with'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'us?'],
                    ],
                ],
                [
                    'label' => 'Labels on the artwork',
                    'description' => 'Each label sits on its own figure in the artwork, so keep them short.',
                    'fields' => [
                        'label_1' => ['type' => 'text', 'label' => 'Top left', 'default' => 'Individual Beauty Consumers'],
                        'label_2' => ['type' => 'text', 'label' => 'Top centre', 'default' => 'Retail Customers'],
                        'label_3' => ['type' => 'text', 'label' => 'Top right', 'default' => 'Wholesale Buyers'],
                        'label_4' => ['type' => 'text', 'label' => 'Middle left', 'default' => 'Beauty Entrepreneurs'],
                        'label_5' => ['type' => 'text', 'label' => 'Middle right', 'default' => 'Beauty Product Resellers'],
                        'label_6' => ['type' => 'text', 'label' => 'Bottom left', 'default' => 'Makeup Artists & Beauty Professionals'],
                        'label_7' => ['type' => 'text', 'label' => 'Bottom centre', 'default' => 'Spa & Massage Businesses'],
                        'label_8' => ['type' => 'text', 'label' => 'Bottom right', 'default' => 'Salon Owners'],
                    ],
                ],
            ],
        ],

        'wholesale' => [
            'label' => 'Wholesale',
            'description' => 'The pitch to salons, shops and resellers.',
            'anchor' => 'wholesale',
            'groups' => [
                [
                    'label' => 'Photo',
                    'fields' => [
                        'photo' => ['type' => 'image', 'label' => 'Wide photo under the two cards', 'default' => '/images/wholesale-strip-1200.jpg', 'alt' => 'Aromatic spa massage oil and botanical bath scrub on a marble surface, lit by candles', 'help' => 'Shown as a wide strip, about four times wider than tall.'],
                    ],
                ],
                [
                    'label' => 'Heading',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'Wholesale'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'Stock your beauty'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'business'],
                        'intro' => ['type' => 'textarea', 'label' => 'Intro', 'default' => 'Retailer, reseller, salon owner, spa operator or beauty entrepreneur — you can buy here in bulk. Starting out or restocking, message us and we will tell you what we have and what it costs.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Get wholesale prices'],
                        'button_note' => ['type' => 'text', 'label' => 'Note under the button', 'default' => 'Opens WhatsApp with a message you can fill in.'],
                        'enquiry' => ['type' => 'textarea', 'label' => 'WhatsApp message the button starts', 'default' => 'Hello Milkyway Cosmetics Stores. I run a [salon / shop / resale business] in [area] and I would like wholesale prices for [category]. I buy roughly [quantity] at a time.'],
                    ],
                ],
                [
                    'label' => 'Cards',
                    'fields' => [
                        'ask_title' => ['type' => 'text', 'label' => 'Light card title', 'default' => 'Ask us about'],
                        'ask_items' => ['type' => 'lines', 'label' => 'Light card list', 'default' => "Available products\nWholesale prices\nMinimum quantities\nCurrent stock\nBulk orders\nDelivery options"],
                        'tell_title' => ['type' => 'text', 'label' => 'Dark card title', 'default' => 'Tell us'],
                        'tell_items' => ['type' => 'lines', 'label' => 'Dark card list', 'default' => "The kind of business you run\nWhere you are based\nWhich categories you stock\nRoughly how much you buy at a time"],
                        'tell_note' => ['type' => 'text', 'label' => 'Dark card footnote', 'default' => 'Four lines in your first message saves a day of back and forth.', 'optional' => true],
                    ],
                ],
            ],
        ],

        'how' => [
            'label' => 'How it works',
            'description' => 'The five steps from browsing to delivery.',
            'anchor' => 'how',
            'groups' => [
                [
                    'label' => 'Photos',
                    'description' => 'Three portraits hanging beside the heading. Only shown on larger screens.',
                    'fields' => [
                        'photo_1' => ['type' => 'image', 'label' => 'Left portrait', 'default' => '/images/how/a.jpg', 'alt' => ''],
                        'photo_2' => ['type' => 'image', 'label' => 'Centre portrait (longer)', 'default' => '/images/how/b.jpg', 'alt' => ''],
                        'photo_3' => ['type' => 'image', 'label' => 'Right portrait', 'default' => '/images/how/c.jpg', 'alt' => ''],
                    ],
                ],
                [
                    'label' => 'Heading',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'How it works'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'From browsing to your'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'doorstep'],
                        'intro' => ['type' => 'textarea', 'label' => 'Intro', 'default' => 'There is no checkout to fight with. You message us, we confirm what is in stock and what it costs, then we arrange delivery.'],
                    ],
                ],
                [
                    'label' => 'Steps',
                    'fields' => [
                        'step_1_title' => ['type' => 'text', 'label' => 'Step 1 title', 'default' => 'Browse products'],
                        'step_1_text' => ['type' => 'textarea', 'label' => 'Step 1 text', 'default' => "Find the beauty or personal-care products you're looking for."],
                        'step_2_title' => ['type' => 'text', 'label' => 'Step 2 title', 'default' => 'Check availability'],
                        'step_2_text' => ['type' => 'textarea', 'label' => 'Step 2 text', 'default' => 'Product availability and prices may vary, so we confirm before anything is agreed.'],
                        'step_3_title' => ['type' => 'text', 'label' => 'Step 3 title', 'default' => 'Contact us'],
                        'step_3_text' => ['type' => 'textarea', 'label' => 'Step 3 text', 'default' => 'Send us a message on WhatsApp for enquiries or orders.'],
                        'step_4_title' => ['type' => 'text', 'label' => 'Step 4 title', 'default' => 'Confirm your order'],
                        'step_4_text' => ['type' => 'textarea', 'label' => 'Step 4 text', 'default' => 'We give you the details you need, including what delivery will involve.'],
                        'step_5_title' => ['type' => 'text', 'label' => 'Step 5 title', 'default' => 'Receive your order'],
                        'step_5_text' => ['type' => 'textarea', 'label' => 'Step 5 text', 'default' => 'Delivery is arranged based on where you are.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Start an order on WhatsApp'],
                    ],
                ],
            ],
        ],

        'delivery' => [
            'label' => 'Delivery',
            'description' => 'Where you deliver, drawn as orbits out from the shop.',
            'anchor' => 'delivery',
            'groups' => [
                [
                    'label' => 'Text',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'Delivery'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'We deliver well past'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'Lagos'],
                        'intro' => ['type' => 'textarea', 'label' => 'Intro', 'default' => 'We cater to customers within and outside Lagos, subject to arrangement and destination. Tell us where you are and we will work it out.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Ask about delivery'],
                        'shop_label' => ['type' => 'text', 'label' => 'Label at the centre of the orbits', 'default' => 'The shop · Amuwo-Odofin'],
                        'other_label' => ['type' => 'text', 'label' => 'Label for other places', 'default' => 'Other locations — ask us'],
                    ],
                ],
            ],
        ],

        'about' => [
            'label' => 'About us',
            'description' => 'Who you are, where to find you and what you stock.',
            'anchor' => 'about',
            'groups' => [
                [
                    'label' => 'Photo',
                    'fields' => [
                        'photo' => ['type' => 'image', 'label' => 'Photo beside the statement', 'default' => '/images/categories/skincare2/drteals.jpg', 'alt' => "Dr Teal's body care range, part of Milkyway's real stock"],
                    ],
                ],
                [
                    'label' => 'Heading and statement',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'About us'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'Based in Lagos. Stocked for'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'everyone'],
                        'statement' => ['type' => 'textarea', 'label' => 'Statement', 'default' => 'Milkyway Cosmetics Stores is a beauty, cosmetics and personal-care business in Amuwo-Odofin, Lagos. We distribute health, beauty, skincare, body-enhancement and spa products — wholesale and retail — to individuals and to the businesses that stock them.'],
                        'goal_label' => ['type' => 'text', 'label' => 'Goal label', 'default' => 'Our goal is simple'],
                        'goal' => ['type' => 'textarea', 'label' => 'Goal', 'default' => 'To make quality beauty and personal-care products accessible, with service that suits both individual customers and beauty businesses.'],
                    ],
                ],
                [
                    'label' => 'Cards',
                    'fields' => [
                        'where_title' => ['type' => 'text', 'label' => 'Location card title', 'default' => 'Where to find us'],
                        'what_title' => ['type' => 'text', 'label' => 'Products card title', 'default' => 'What we distribute'],
                        'what_items' => ['type' => 'lines', 'label' => 'Products card list', 'default' => "Health\nBeauty\nSkincare\nBody Enhancement\nSpa"],
                        'reach_title' => ['type' => 'text', 'label' => 'Reach card title', 'default' => 'How far we reach'],
                        'reach_text' => ['type' => 'textarea', 'label' => 'Reach card text', 'default' => 'Delivery across Lagos, Ogun and Abuja.'],
                        'reach_note' => ['type' => 'textarea', 'label' => 'Reach card note', 'default' => 'Our customers go further still — as far as Accra, Ghana.', 'optional' => true],
                    ],
                ],
            ],
        ],

        'contact' => [
            'label' => 'Contact',
            'description' => 'The shop details, buttons and map. The details themselves are under Business details.',
            'anchor' => 'contact',
            'groups' => [
                [
                    'label' => 'Text',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small label', 'default' => 'Contact Us'],
                        'heading' => ['type' => 'text', 'label' => 'Heading start', 'default' => 'Come and see'],
                        'heading_accent' => ['type' => 'text', 'label' => 'Handwritten word', 'default' => 'us'],
                        'intro' => ['type' => 'textarea', 'label' => 'Intro', 'default' => 'Visit our physical store in Tradefair Complex, Lagos or get in touch directly for wholesale & retail orders.'],
                        'shop_label' => ['type' => 'text', 'label' => 'Address label', 'default' => 'The Shop'],
                        'hours_label' => ['type' => 'text', 'label' => 'Hours label', 'default' => 'Opening Hours'],
                        'phone_label' => ['type' => 'text', 'label' => 'Phone label', 'default' => 'Phone & Enquiries'],
                        'call_button' => ['type' => 'text', 'label' => 'Call button', 'default' => 'Call us'],
                        'whatsapp_button' => ['type' => 'text', 'label' => 'WhatsApp button', 'default' => 'WhatsApp us'],
                        'directions_button' => ['type' => 'text', 'label' => 'Directions button', 'default' => 'Get directions'],
                    ],
                ],
            ],
        ],

        'footer' => [
            'label' => 'Footer',
            'description' => 'The dark band at the bottom of every shop page.',
            'anchor' => 'site-footer',
            'groups' => [
                [
                    'label' => 'Text',
                    'fields' => [
                        'blurb' => ['type' => 'textarea', 'label' => 'About line under the logo', 'default' => 'Milkyway Cosmetics Stores supplies quality skincare, beauty, body enhancement and spa products at wholesale and retail prices, from Amuwo-Odofin, Lagos.'],
                        'whatsapp_label' => ['type' => 'text', 'label' => 'WhatsApp chip', 'default' => 'Chat on WhatsApp · 24/7 Available'],
                        'store_info' => ['type' => 'lines', 'label' => 'Store info column', 'default' => "C003 Bornu Plaza\nTradefair, Lagos\nOpen 24/7 Mon–Sun\nLagos · Abuja · Accra"],
                        'bottom_line' => ['type' => 'text', 'label' => 'Line beside the copyright', 'default' => 'Tradefair Complex, Lagos · Wholesale & Retail', 'optional' => true],
                    ],
                ],
            ],
        ],

        'business' => [
            'label' => 'Business details',
            'description' => 'Phone, WhatsApp, address and hours, used across the whole site and in Google results.',
            'anchor' => 'contact',
            'groups' => [
                [
                    'label' => 'Contact',
                    'fields' => [
                        'phone' => ['type' => 'text', 'label' => 'Phone number', 'default' => '+234 816 182 3482', 'help' => 'As customers should see it. The call buttons dial the same number.'],
                        'whatsapp' => ['type' => 'text', 'label' => 'WhatsApp number', 'default' => '2348161823482', 'help' => 'Country code first, digits only, for example 2348161823482.'],
                        'whatsapp_message' => ['type' => 'textarea', 'label' => 'Default WhatsApp message', 'default' => 'Hello Milkyway Cosmetics Stores, I would like to make an enquiry about your products.'],
                    ],
                ],
                [
                    'label' => 'Location',
                    'fields' => [
                        'address_line' => ['type' => 'text', 'label' => 'Street address', 'default' => 'C003 Bornu Plaza, Tradefair Complex'],
                        'address_area' => ['type' => 'text', 'label' => 'Area, city and country', 'default' => 'Amuwo-Odofin, Lagos, Nigeria'],
                        'hours' => ['type' => 'text', 'label' => 'Opening hours', 'default' => 'Open 24 Hours, Monday to Sunday'],
                        'delivery_areas' => ['type' => 'lines', 'label' => 'Delivery areas', 'default' => "Lagos\nOgun\nAbuja\nAccra, Ghana", 'help' => 'One place per line. The first four are drawn on the delivery orbits.'],
                    ],
                ],
            ],
        ],

    ],

    /*
    | Where uploaded photos go in Cloudinary.
    */
    'folder' => 'milky-way/site',

];
