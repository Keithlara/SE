<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  $manual_sections = [
    [
      'id' => 'panel-basics',
      'title' => 'Panel Basics',
      'summary' => 'How the admin panel is arranged, what the badges mean, and where account tools now live.',
      'cards' => [
        [
          'icon' => 'bi-compass',
          'name' => 'How the panel is organized',
          'route' => 'Global navigation',
          'tag' => 'Overview',
          'description' => 'The left sidebar groups tools by work area. The top bar shows urgent counts and quick links. The profile menu now holds appearance settings, password, and logout.',
          'points' => [
            'Start on Dashboard when you need the full system picture.',
            'Use the sidebar for full modules and deeper management pages.',
            'Use the profile menu for appearance, password changes, and sign out.'
          ]
        ],
        [
          'icon' => 'bi-bell',
          'name' => 'Badges and alerts',
          'route' => 'Top bar and sidebar badges',
          'tag' => 'Attention',
          'description' => 'Red number badges show work that needs action right away, such as pending bookings, refund requests, and unread support tickets.',
          'points' => [
            'Click the badge links to jump directly into the queue that needs attention.',
            'Badge counts update from live booking and support records.',
            'If a module is missing, it may be hidden by permissions for that account.'
          ]
        ],
        [
          'icon' => 'bi-shield-lock',
          'name' => 'Admin and staff access',
          'route' => 'Role-based permissions',
          'tag' => 'Permissions',
          'description' => 'Admins normally see the full panel. Staff accounts only see the modules assigned to them through Staff Permissions.',
          'points' => [
            'Use staff accounts for day-to-day work without exposing every module.',
            'Keep high-risk tools like backups, settings, and access control limited to trusted admins.',
            'If a staff member cannot see a page, check their permission list first.'
          ]
        ],
        [
          'icon' => 'bi-stars',
          'name' => 'Appearance and account tools',
          'route' => 'Profile dropdown',
          'tag' => 'Account',
          'description' => 'The admin profile menu includes the compact Appearance panel, Change Password, and Log Out.',
          'points' => [
            'Appearance changes affect both admin and staff workspace themes.',
            'Use Change Password regularly for account security.',
            'Log out after working on shared or public computers.'
          ]
        ]
      ]
    ],
    [
      'id' => 'daily-workflows',
      'title' => 'Daily Workflows',
      'summary' => 'The most common admin jobs from morning check to end-of-day review.',
      'cards' => [
        [
          'icon' => 'bi-calendar-check',
          'name' => 'Approve a new booking',
          'route' => 'Bookings > New Bookings',
          'tag' => 'Workflow',
          'description' => 'Review the guest details, stay dates, room assignment, proof of payment, extras, and promo before confirming a reservation.',
          'points' => [
            'Open the pending booking queue and inspect the submitted details.',
            'Verify the proof of payment and room number before confirming.',
            'Only mark a booking as confirmed when the payment proof and stay details are correct.'
          ]
        ],
        [
          'icon' => 'bi-arrow-counterclockwise',
          'name' => 'Process a refund request',
          'route' => 'Bookings > Refund Bookings',
          'tag' => 'Workflow',
          'description' => 'Track cancelled bookings that still need refund handling, proof review, or admin approval.',
          'points' => [
            'Open pending refund requests and review the booking and refund details.',
            'Check refund proof or status before marking the request as processed.',
            'Use the booking history and support notes if the guest has ongoing questions.'
          ]
        ],
        [
          'icon' => 'bi-headset',
          'name' => 'Handle guest support',
          'route' => 'Service > Service Center',
          'tag' => 'Workflow',
          'description' => 'Use the Service Center for ticket replies, escalation, guest notes, canned replies, and email history.',
          'points' => [
            'Open unread or escalated tickets first.',
            'Reply inside the ticket thread so the conversation stays attached to the guest and booking.',
            'Add internal notes when staff needs context that should not be visible to the guest.'
          ]
        ],
        [
          'icon' => 'bi-hdd-rack',
          'name' => 'Prepare safe admin changes',
          'route' => 'Utilities > Backup & Restore / Activity Logs',
          'tag' => 'Safety',
          'description' => 'Before large content or settings changes, protect the system with a backup and make sure actions can be traced afterward.',
          'points' => [
            'Create a backup before bulk edits, layout changes, or data imports.',
            'Use Activity Logs to confirm who changed a record and when.',
            'Use Archives for recovery-style work instead of deleting data permanently.'
          ]
        ]
      ]
    ],
    [
      'id' => 'dashboard-bookings',
      'title' => 'Dashboard and Bookings',
      'summary' => 'The core operational pages used to manage reservations, room occupancy, and booking history.',
      'cards' => [
        [
          'icon' => 'bi-speedometer2',
          'name' => 'Dashboard',
          'route' => 'dashboard.php',
          'tag' => 'Main hub',
          'description' => 'The dashboard shows room occupancy, items that need attention, booking counts, revenue, and quick jump points to high-priority modules.',
          'points' => [
            'Use this as the first page to review the day.',
            'Watch the occupancy map for room availability and active stays.',
            'Use the attention cards to jump straight into new bookings, refunds, queries, and reviews.'
          ]
        ],
        [
          'icon' => 'bi-calendar-plus',
          'name' => 'New Bookings',
          'route' => 'new_bookings.php',
          'tag' => 'Bookings',
          'description' => 'This queue contains pending reservations and confirmed bookings, along with room assignment and proof verification actions.',
          'points' => [
            'Search pending and confirmed bookings with the built-in filters.',
            'Use this page to confirm, reject, or review booking submissions.',
            'Always verify stay dates, amount due, payment proof, and room availability before approving.'
          ]
        ],
        [
          'icon' => 'bi-journal-text',
          'name' => 'Booking Records',
          'route' => 'booking_records.php',
          'tag' => 'History',
          'description' => 'Booking Records is the cleaner archive-style view for completed, refunded, and processed reservation records.',
          'points' => [
            'Use filters to review bookings by month, year, status, and search terms.',
            'Open proof and transaction details when auditing a past booking.',
            'Use this page for review and reporting, not for active approval work.'
          ]
        ],
        [
          'icon' => 'bi-calendar3',
          'name' => 'Booking Calendar',
          'route' => 'booking_calendar.php',
          'tag' => 'Planning',
          'description' => 'The booking calendar gives a room-by-room date view of booked, pending, cancelled, and blocked dates.',
          'points' => [
            'Use it to understand occupancy across the month at a glance.',
            'Filter by room when checking availability or planning maintenance.',
            'Review blocked dates carefully before promising room availability to guests.'
          ]
        ]
      ]
    ],
    [
      'id' => 'service-reports',
      'title' => 'Service and Reports',
      'summary' => 'Support handling, guest communication, and reporting pages used for follow-up and business review.',
      'cards' => [
        [
          'icon' => 'bi-life-preserver',
          'name' => 'Service Center',
          'route' => 'support_center.php',
          'tag' => 'Support',
          'description' => 'The Service Center combines support tickets, reply threads, guest notes, canned replies, and email history in one workspace.',
          'points' => [
            'Update ticket status as Open, Pending, Resolved, or Escalated.',
            'Use canned replies to keep responses fast and consistent.',
            'Check email history when a guest claims they did not receive a confirmation or update.'
          ]
        ],
        [
          'icon' => 'bi-bar-chart-line',
          'name' => 'All Time Reports',
          'route' => 'all_time_reports.php',
          'tag' => 'Analytics',
          'description' => 'Reports shows revenue, occupancy, booking volume, top room, top add-on, repeat guests, and refund rate across selectable date ranges.',
          'points' => [
            'Adjust the date range and granularity to review performance trends.',
            'Use Top Room and Top Add-on to guide pricing and content updates.',
            'Check refund rate and repeat guests to understand operational quality.'
          ]
        ],
        [
          'icon' => 'bi-receipt',
          'name' => 'Transactions',
          'route' => 'transaction.php',
          'tag' => 'Finance',
          'description' => 'Transactions is the payment history view for reviewing what was paid, when it was paid, and which booking it belongs to.',
          'points' => [
            'Use this page when reconciling downpayments and booking totals.',
            'Cross-check payment records with booking and refund pages when a dispute happens.',
            'Review transaction details before manually adjusting any payment-related record.'
          ]
        ]
      ]
    ]
  ];

  array_push(
    $manual_sections,
    [
      'id' => 'access-users',
      'title' => 'Access and Users',
      'summary' => 'Pages used to manage guest accounts, admin or staff accounts, and permission boundaries.',
      'cards' => [
        [
          'icon' => 'bi-people-fill',
          'name' => 'User Accounts',
          'route' => 'users.php',
          'tag' => 'Guests',
          'description' => 'User Accounts is the guest-management page for reviewing registered users and account status.',
          'points' => [
            'Use this to review customer account information and status.',
            'Check guest records when a booking or support issue is tied to account data.',
            'Avoid editing guest information blindly without confirming the request.'
          ]
        ],
        [
      'icon' => 'bi-person-badge',
          'name' => 'System Users',
          'route' => 'manage_users.php / create_user.php',
          'tag' => 'Admin staff',
          'description' => 'System Users lets admins create, edit, and manage admin or staff accounts for the panel itself.',
          'points' => [
            'Create accounts only for trusted team members who need panel access.',
            'Use staff accounts for limited operational work instead of sharing the admin account.',
            'Update passwords or roles carefully to avoid locking out the wrong user.'
          ]
        ],
        [
          'icon' => 'bi-sliders',
          'name' => 'Staff Permissions',
          'route' => 'staff_permissions.php',
          'tag' => 'Access control',
          'description' => 'Staff Permissions controls which modules staff can see and manage, such as bookings, reports, content, support, and utilities.',
          'points' => [
            'Give each staff user only the modules they actually need.',
            'Review permissions when a staff member should not see sensitive pages.',
            'Keep utilities, backups, and settings restricted to trusted admins whenever possible.'
          ]
        ],
        [
          'icon' => 'bi-key',
          'name' => 'Password and account safety',
          'route' => 'Profile menu > Change Password',
          'tag' => 'Security',
          'description' => 'Use the profile dropdown to change the current admin password and keep the session secure.',
          'points' => [
            'Update passwords regularly, especially after staff changes.',
            'Log out after using a shared or public device.',
            'Never share one admin login across multiple people if separate staff accounts can be used.'
          ]
        ]
      ]
    ],
    [
      'id' => 'content-management',
      'title' => 'Content Management',
      'summary' => 'Pages used to control what guests see, what they can book, and which offers or rules apply.',
      'cards' => [
        [
          'icon' => 'bi-door-open',
          'name' => 'Manage Rooms',
          'route' => 'rooms.php',
          'tag' => 'Rooms',
          'description' => 'Manage Rooms controls room records, room images, pricing, capacity, status, and the details guests see on the booking side.',
          'points' => [
            'Update room name, price, quantity, and images carefully because bookings depend on them.',
            'Disable or remove rooms only after checking whether future bookings are affected.',
            'Use room details that are clear and consistent for guests and staff.'
          ]
        ],
        [
          'icon' => 'bi-stars',
          'name' => 'Features and Facilities',
          'route' => 'features_facilities.php',
          'tag' => 'Amenities',
          'description' => 'This page manages the features and facilities that can be attached to rooms, such as amenity labels and room selling points.',
          'points' => [
            'Keep naming consistent so rooms look professional and easy to compare.',
            'Use facilities for concrete amenities guests expect to see.',
            'Use features for room highlights or differentiators.'
          ]
        ],
        [
          'icon' => 'bi-plus-circle',
          'name' => 'Extras and Booking Rules',
          'route' => 'extras.php',
          'tag' => 'Add-ons',
          'description' => 'Extras and Booking Rules manages paid add-ons and the booking policy or house rules shown to guests during checkout.',
          'points' => [
            'Use extras for optional paid add-ons such as mattress, pillow, blanket, or transfers.',
            'Remember that extras can affect booking totals and guest billing.',
            'Write booking rules clearly because guests see them during checkout and dispute handling.'
          ]
        ],
        [
          'icon' => 'bi-images',
          'name' => 'Carousel',
          'route' => 'carousel.php',
          'tag' => 'Homepage',
          'description' => 'Carousel controls the homepage slider images that guests see first.',
          'points' => [
            'Use high-quality images sized consistently for a clean homepage.',
            'Remove broken or outdated images to avoid missing-image errors.',
            'Refresh visuals when rooms, packages, or seasonal offers change.'
          ]
        ],
        [
          'icon' => 'bi-star-half',
          'name' => 'Ratings and Reviews',
          'route' => 'rate_review.php',
          'tag' => 'Feedback',
          'description' => 'Ratings and Reviews helps admins monitor guest feedback and new review submissions.',
          'points' => [
            'Review new feedback regularly to catch service issues early.',
            'Use reviews to spot recurring room, facility, or service complaints.',
            'Coordinate with the Service Center when a review turns into a support issue.'
          ]
        ],
        [
          'icon' => 'bi-tags',
          'name' => 'Promo Codes',
          'route' => 'promo_codes.php',
          'tag' => 'Discounts',
          'description' => 'Promo Codes manages fixed or percentage discounts, minimum spend, max discount, date range, usage limit, and active status.',
          'points' => [
            'Check date range and active status before announcing a promo.',
            'Use minimum amount and max discount to keep promotions controlled.',
            'Pause or activate promos instead of deleting them when you still want the history.'
          ]
        ]
      ]
    ],
    [
      'id' => 'utilities',
      'title' => 'Utilities',
      'summary' => 'Operational tools for backups, recovery, system settings, and internal reference.',
      'cards' => [
        [
          'icon' => 'bi-archive',
          'name' => 'Archives',
          'route' => 'Archives.php',
          'tag' => 'Recovery',
          'description' => 'Archives stores old or archived bookings, rooms, users, and query records so they can be reviewed or restored when needed.',
          'points' => [
            'Use archives when a record should be hidden from daily work but not permanently lost.',
            'Restore carefully so old records do not conflict with current operations.',
            'Search by guest, room, or date when recovering a specific archived item.'
          ]
        ],
        [
          'icon' => 'bi-hdd-rack',
          'name' => 'Backup and Restore',
          'route' => 'backup_restore.php',
          'tag' => 'Protection',
          'description' => 'Backup and Restore is the safety tool for exporting and restoring system data before large changes or in case of recovery work.',
          'points' => [
            'Create a backup before major settings changes, imports, or content overhauls.',
            'Restore only from a backup you trust and understand.',
            'Avoid restoring over live data without confirming what will be overwritten.'
          ]
        ],
        [
          'icon' => 'bi-list-check',
          'name' => 'Activity Logs',
          'route' => 'activity_logs.php',
          'tag' => 'Audit trail',
          'description' => 'Activity Logs records admin-side actions so the team can trace important changes and operational events.',
          'points' => [
            'Check logs when a booking, setting, or content record changed unexpectedly.',
            'Use logs to answer who changed what and when.',
            'Review audit history before undoing a change manually.'
          ]
        ],
        [
          'icon' => 'bi-gear',
          'name' => 'Settings',
          'route' => 'settings.php',
          'tag' => 'Site-wide setup',
          'description' => 'Settings manages general site title and about text, shutdown mode, contact information, social links, payment proof instructions, QR references, and other global admin-managed information.',
          'points' => [
            'Use General Settings for site identity and homepage text.',
            'Use Shutdown Website only when you need to temporarily stop new bookings.',
            'Keep payment numbers, QR codes, and contact details current so booking and support flows work smoothly.'
          ]
        ],
        [
          'icon' => 'bi-journal-bookmark',
          'name' => 'Admin Manual',
          'route' => 'manual.php',
          'tag' => 'Reference',
          'description' => 'This manual page explains how the admin panel works, what each module does, and which workflows staff should follow.',
          'points' => [
            'Use the search box to find a module or task quickly.',
            'Open the section cards for high-level instructions before changing data.',
            'Keep this page updated when the panel gains new modules or workflows.'
          ]
        ]
      ]
    ],
    [
      'id' => 'best-practices',
      'title' => 'Best Practices',
      'summary' => 'Rules of thumb that keep the panel clean, accurate, and safer for staff to use.',
      'cards' => [
        [
          'icon' => 'bi-check2-circle',
          'name' => 'Operational habits',
          'route' => 'Daily admin use',
          'tag' => 'Quality',
          'description' => 'Good habits reduce booking errors, refund disputes, and staff confusion.',
          'points' => [
            'Confirm payment proof before approving a booking.',
            'Use notes, support tickets, and logs instead of relying on memory.',
            'Keep room content, extras, promos, and payment instructions consistent with the real operation.'
          ]
        ],
        [
          'icon' => 'bi-exclamation-triangle',
          'name' => 'Before making high-impact changes',
          'route' => 'Backups, settings, permissions',
          'tag' => 'Safety',
          'description' => 'Some actions affect the full system and should be handled carefully.',
          'points' => [
            'Create a backup before bulk edits or structural admin changes.',
            'Review permissions before giving staff access to utilities or settings.',
            'Use Archives or status changes instead of deleting records permanently whenever possible.'
          ]
        ]
      ]
    ]
  );

  $manual_visuals = [
    [
      'icon' => 'bi-speedometer2',
      'title' => 'Dashboard snapshot',
      'copy' => 'A quick daily view of occupancy, urgent items, and revenue movement.',
      'chips' => [
        ['text' => 'Occupied 12', 'tone' => 'accent'],
        ['text' => 'Refunds 3', 'tone' => 'danger'],
        ['text' => 'Reports', 'tone' => 'muted']
      ],
      'lines' => ['Rooms and occupancy', 'Attention cards', 'Today\'s performance']
    ],
    [
      'icon' => 'bi-calendar-plus',
      'title' => 'Booking queue view',
      'copy' => 'Where admins review proofs, room numbers, and booking approval work.',
      'chips' => [
        ['text' => 'Pending', 'tone' => 'warning'],
        ['text' => 'Proof on file', 'tone' => 'info'],
        ['text' => 'Confirm', 'tone' => 'success']
      ],
      'lines' => ['Guest details', 'Stay dates and room', 'Approve or reject']
    ],
    [
      'icon' => 'bi-life-preserver',
      'title' => 'Support thread',
      'copy' => 'A linked conversation with ticket status, booking ID, and replies in one place.',
      'chips' => [
        ['text' => 'Open', 'tone' => 'danger'],
        ['text' => 'Booking linked', 'tone' => 'accent'],
        ['text' => 'Resolved', 'tone' => 'success']
      ],
      'lines' => ['Guest message', 'Staff reply', 'Status update']
    ],
    [
      'icon' => 'bi-gear',
      'title' => 'Utilities and setup',
      'copy' => 'The safe workspace for backups, logs, settings, and internal reference pages.',
      'chips' => [
        ['text' => 'Backup first', 'tone' => 'warning'],
        ['text' => 'Logs', 'tone' => 'muted'],
        ['text' => 'Settings', 'tone' => 'accent']
      ],
      'lines' => ['Create backup', 'Check logs', 'Update system info']
    ]
  ];

  function getManualCardSample(array $card): ?array
  {
    $lookup = strtolower(($card['route'] ?? '') . ' ' . ($card['name'] ?? ''));

    if (strpos($lookup, 'new bookings') !== false || strpos($lookup, 'approve a new booking') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Pending booking queue',
        'chips' => [
          ['text' => 'Pending', 'tone' => 'warning'],
          ['text' => 'Proof ready', 'tone' => 'info'],
          ['text' => 'Room 7', 'tone' => 'accent']
        ],
        'steps' => ['Open the booking card', 'Check proof and dates', 'Confirm the reservation']
      ];
    }

    if (strpos($lookup, 'refund') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Refund handling flow',
        'chips' => [
          ['text' => 'Cancelled', 'tone' => 'danger'],
          ['text' => 'Refund proof', 'tone' => 'info'],
          ['text' => 'Processed', 'tone' => 'success']
        ],
        'steps' => ['Review cancellation', 'Check refund status', 'Mark as completed']
      ];
    }

    if (strpos($lookup, 'support_center.php') !== false || strpos($lookup, 'service center') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Ticket thread view',
        'chips' => [
          ['text' => 'Open', 'tone' => 'danger'],
          ['text' => 'ORD_99431650', 'tone' => 'accent'],
          ['text' => 'Reply sent', 'tone' => 'success']
        ],
        'steps' => ['Read the guest issue', 'Reply inside the thread', 'Update the ticket status']
      ];
    }

    if (strpos($lookup, 'booking calendar') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Room date colors',
        'chips' => [
          ['text' => 'Booked', 'tone' => 'accent'],
          ['text' => 'Pending', 'tone' => 'warning'],
          ['text' => 'Blocked', 'tone' => 'danger']
        ],
        'steps' => ['Pick a month', 'Filter by room', 'Inspect room availability']
      ];
    }

    if (strpos($lookup, 'promo code') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Promo setup',
        'chips' => [
          ['text' => 'SUMMER10', 'tone' => 'accent'],
          ['text' => '10% off', 'tone' => 'success'],
          ['text' => 'Active', 'tone' => 'info']
        ],
        'steps' => ['Set the code', 'Add limits and date range', 'Activate when ready']
      ];
    }

    if (strpos($lookup, 'settings.php') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Global setup items',
        'chips' => [
          ['text' => 'Site title', 'tone' => 'muted'],
          ['text' => 'Payment QR', 'tone' => 'accent'],
          ['text' => 'Shutdown', 'tone' => 'warning']
        ],
        'steps' => ['Update contact details', 'Check payment references', 'Save carefully']
      ];
    }

    if (strpos($lookup, 'backup_restore.php') !== false || strpos($lookup, 'activity logs') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Safe admin change routine',
        'chips' => [
          ['text' => 'Backup', 'tone' => 'warning'],
          ['text' => 'Make changes', 'tone' => 'accent'],
          ['text' => 'Check logs', 'tone' => 'success']
        ],
        'steps' => ['Create a backup first', 'Complete the admin task', 'Verify the action in logs']
      ];
    }

    if (strpos($lookup, 'dashboard.php') !== false) {
      return [
        'title' => 'Actual Example',
        'label' => 'Daily dashboard check',
        'chips' => [
          ['text' => 'New bookings', 'tone' => 'warning'],
          ['text' => 'Refunds', 'tone' => 'danger'],
          ['text' => 'Reports', 'tone' => 'accent']
        ],
        'steps' => ['Scan urgent counts', 'Open the needed queue', 'Return to dashboard after updates']
      ];
    }

    return null;
  }

  function renderManualVisual(array $visual): void
  {
    ?>
    <div class="col-12 col-md-6">
      <article class="manual-preview-card h-100">
        <div class="manual-preview-window">
          <div class="manual-preview-bar">
            <span></span><span></span><span></span>
          </div>
          <div class="manual-preview-body">
            <div class="manual-preview-head">
              <i class="bi <?php echo htmlspecialchars($visual['icon']); ?>"></i>
              <strong><?php echo htmlspecialchars($visual['title']); ?></strong>
            </div>
            <div class="manual-preview-chips">
              <?php foreach ($visual['chips'] as $chip): ?>
                <span class="manual-sample-chip is-<?php echo htmlspecialchars($chip['tone']); ?>"><?php echo htmlspecialchars($chip['text']); ?></span>
              <?php endforeach; ?>
            </div>
            <div class="manual-preview-lines">
              <?php foreach ($visual['lines'] as $line): ?>
                <div class="manual-preview-line"><?php echo htmlspecialchars($line); ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="manual-preview-copy"><?php echo htmlspecialchars($visual['copy']); ?></div>
      </article>
    </div>
    <?php
  }

  function renderManualCard(array $card): void
  {
    $search_parts = [$card['name'], $card['route'], $card['tag'], $card['description']];
    foreach ($card['points'] as $point) {
      $search_parts[] = $point;
    }
    $sample = getManualCardSample($card);
    if ($sample) {
      $search_parts[] = $sample['label'];
      foreach ($sample['chips'] as $chip) {
        $search_parts[] = $chip['text'];
      }
      foreach ($sample['steps'] as $step) {
        $search_parts[] = $step;
      }
    }
    $search_blob = htmlspecialchars(strtolower(implode(' ', $search_parts)), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="col-12 col-md-6">
      <article class="manual-card h-100" data-manual-search="<?php echo $search_blob; ?>">
        <div class="manual-card-top">
          <div class="manual-icon">
            <i class="bi <?php echo htmlspecialchars($card['icon']); ?>"></i>
          </div>
          <div class="manual-card-head">
            <div class="manual-card-route"><?php echo htmlspecialchars($card['route']); ?></div>
            <h5 class="manual-card-title"><?php echo htmlspecialchars($card['name']); ?></h5>
          </div>
          <span class="manual-card-tag"><?php echo htmlspecialchars($card['tag']); ?></span>
        </div>
        <p class="manual-card-copy"><?php echo htmlspecialchars($card['description']); ?></p>
        <?php if ($sample): ?>
          <div class="manual-sample-box">
            <div class="manual-sample-headline">
              <span><?php echo htmlspecialchars($sample['title']); ?></span>
              <strong><?php echo htmlspecialchars($sample['label']); ?></strong>
            </div>
            <div class="manual-sample-chips">
              <?php foreach ($sample['chips'] as $chip): ?>
                <span class="manual-sample-chip is-<?php echo htmlspecialchars($chip['tone']); ?>"><?php echo htmlspecialchars($chip['text']); ?></span>
              <?php endforeach; ?>
            </div>
            <div class="manual-sample-steps">
              <?php foreach ($sample['steps'] as $step): ?>
                <div class="manual-sample-step"><?php echo htmlspecialchars($step); ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        <ul class="manual-points">
          <?php foreach ($card['points'] as $point): ?>
            <li><?php echo htmlspecialchars($point); ?></li>
          <?php endforeach; ?>
        </ul>
      </article>
    </div>
    <?php
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Admin Manual</title>
  <?php require('inc/links.php'); ?>
  <style>
    .manual-hero {
      background:
        radial-gradient(circle at top left, rgba(var(--admin-accent-rgb), 0.18), transparent 32%),
        linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.92));
      border-radius: 26px;
      padding: 28px;
      color: #fff;
      box-shadow: 0 24px 55px rgba(15, 23, 42, 0.18);
      margin-bottom: 24px;
    }
    .manual-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      font-weight: 700;
      margin-bottom: 14px;
    }
    .manual-hero h3 {
      color: #fff;
      margin-bottom: 10px;
      font-size: 2rem;
      font-weight: 700;
    }
    .manual-hero-copy {
      color: rgba(255, 255, 255, 0.82);
      max-width: 760px;
      line-height: 1.7;
      margin-bottom: 0;
    }
    .manual-hero-meta {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 18px;
    }
    .manual-meta-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.08);
      color: rgba(255, 255, 255, 0.88);
      font-size: 0.85rem;
    }
    .manual-search-card,
    .manual-toc,
    .manual-section {
      background: var(--admin-surface);
      border: 1px solid var(--admin-border);
      border-radius: 22px;
      box-shadow: var(--admin-card-shadow);
    }
    .manual-visual-strip {
      margin-bottom: 22px;
    }
    .manual-preview-card {
      border: 1px solid rgba(var(--admin-accent-rgb), 0.08);
      border-radius: 22px;
      padding: 16px;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), var(--admin-surface-muted));
      box-shadow: var(--admin-card-shadow);
    }
    .manual-preview-window {
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid rgba(var(--admin-accent-rgb), 0.1);
      background: #f8fafc;
      margin-bottom: 12px;
    }
    .manual-preview-bar {
      display: flex;
      gap: 6px;
      padding: 10px 12px;
      background: rgba(15, 23, 42, 0.08);
    }
    .manual-preview-bar span {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.22);
      display: inline-block;
    }
    .manual-preview-body {
      padding: 14px;
    }
    .manual-preview-head {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
      color: var(--admin-text);
    }
    .manual-preview-head i {
      color: var(--admin-accent);
      font-size: 1rem;
    }
    .manual-preview-chips,
    .manual-sample-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 10px;
    }
    .manual-preview-lines {
      display: grid;
      gap: 8px;
    }
    .manual-preview-line {
      border-radius: 12px;
      background: rgba(var(--admin-accent-rgb), 0.06);
      color: var(--admin-text);
      font-size: 0.82rem;
      padding: 9px 12px;
    }
    .manual-preview-copy {
      color: var(--admin-text-muted);
      line-height: 1.65;
      font-size: 0.9rem;
    }
    .manual-search-card {
      padding: 18px;
      margin-bottom: 20px;
    }
    .manual-search-card .form-control {
      min-height: 48px;
      border-radius: 14px;
    }
    .manual-search-note {
      margin-top: 10px;
      font-size: 0.83rem;
      color: var(--admin-text-muted);
    }
    .manual-toc {
      padding: 18px;
      position: sticky;
      top: 96px;
    }
    .manual-toc-title {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--admin-text-muted);
      font-weight: 700;
      margin-bottom: 14px;
    }
    .manual-toc-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      text-decoration: none;
      color: var(--admin-text);
      padding: 10px 12px;
      border-radius: 14px;
      transition: 0.18s ease;
      margin-bottom: 6px;
    }
    .manual-toc-link:hover {
      background: rgba(var(--admin-accent-rgb), 0.08);
      color: var(--admin-accent);
    }
    .manual-toc-link span:last-child {
      font-size: 0.74rem;
      color: var(--admin-text-muted);
    }
    .manual-section {
      padding: 22px;
      margin-bottom: 22px;
    }
    .manual-section-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 18px;
    }
    .manual-section-title {
      margin: 0 0 6px;
      font-size: 1.28rem;
      font-weight: 700;
      color: var(--admin-text);
    }
    .manual-section-copy {
      margin: 0;
      color: var(--admin-text-muted);
      line-height: 1.7;
      max-width: 760px;
    }
    .manual-section-chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(var(--admin-accent-rgb), 0.1);
      color: var(--admin-accent);
      font-size: 0.8rem;
      font-weight: 700;
      white-space: nowrap;
    }
    .manual-card {
      border: 1px solid rgba(var(--admin-accent-rgb), 0.08);
      border-radius: 20px;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), var(--admin-surface-muted));
      padding: 18px;
      transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .manual-card:hover {
      transform: translateY(-2px);
      border-color: rgba(var(--admin-accent-rgb), 0.24);
      box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
    }
    .manual-card-top {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 14px;
    }
    .manual-icon {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(var(--admin-accent-rgb), 0.1);
      color: var(--admin-accent);
      font-size: 1.2rem;
      flex-shrink: 0;
    }
    .manual-card-head {
      min-width: 0;
      flex: 1;
    }
    .manual-card-route {
      font-size: 0.74rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--admin-text-muted);
      font-weight: 700;
      margin-bottom: 4px;
    }
    .manual-card-title {
      margin: 0;
      font-size: 1rem;
      font-weight: 700;
      color: var(--admin-text);
    }
    .manual-card-tag {
      display: inline-flex;
      align-items: center;
      padding: 7px 10px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.06);
      color: var(--admin-text-muted);
      font-size: 0.73rem;
      font-weight: 700;
      flex-shrink: 0;
    }
    .manual-card-copy {
      color: var(--admin-text-muted);
      line-height: 1.72;
      margin-bottom: 12px;
    }
    .manual-sample-box {
      border-radius: 16px;
      border: 1px solid rgba(var(--admin-accent-rgb), 0.12);
      background: rgba(var(--admin-accent-rgb), 0.05);
      padding: 14px;
      margin-bottom: 14px;
    }
    .manual-sample-headline {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
      flex-wrap: wrap;
    }
    .manual-sample-headline span {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--admin-text-muted);
      font-weight: 700;
    }
    .manual-sample-headline strong {
      color: var(--admin-text);
      font-size: 0.88rem;
    }
    .manual-sample-chip {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
      line-height: 1;
    }
    .manual-sample-chip.is-accent {
      background: rgba(var(--admin-accent-rgb), 0.14);
      color: var(--admin-accent);
    }
    .manual-sample-chip.is-success {
      background: rgba(22, 163, 74, 0.12);
      color: #15803d;
    }
    .manual-sample-chip.is-warning {
      background: rgba(217, 119, 6, 0.12);
      color: #b45309;
    }
    .manual-sample-chip.is-danger {
      background: rgba(220, 38, 38, 0.12);
      color: #b91c1c;
    }
    .manual-sample-chip.is-info {
      background: rgba(2, 132, 199, 0.12);
      color: #0369a1;
    }
    .manual-sample-chip.is-muted {
      background: rgba(15, 23, 42, 0.08);
      color: var(--admin-text-muted);
    }
    .manual-sample-steps {
      display: grid;
      gap: 8px;
    }
    .manual-sample-step {
      position: relative;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.72);
      color: var(--admin-text);
      font-size: 0.81rem;
      padding: 9px 12px 9px 34px;
      line-height: 1.55;
    }
    .manual-sample-step::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 12px;
      width: 10px;
      height: 10px;
      border-radius: 999px;
      transform: translateY(-50%);
      background: var(--admin-accent);
      box-shadow: 0 0 0 4px rgba(var(--admin-accent-rgb), 0.12);
    }
    .manual-points {
      margin: 0;
      padding-left: 18px;
      color: var(--admin-text);
    }
    .manual-points li {
      margin-bottom: 8px;
      line-height: 1.65;
    }
    .manual-empty {
      padding: 24px;
      text-align: center;
      border-radius: 18px;
      border: 1px dashed var(--admin-border);
      color: var(--admin-text-muted);
      background: rgba(255, 255, 255, 0.5);
    }
    @media (max-width: 1199px) {
      .manual-toc {
        position: static;
      }
    }
    @media (max-width: 767px) {
      .manual-hero {
        padding: 22px;
      }
      .manual-section {
        padding: 18px;
      }
      .manual-section-head {
        flex-direction: column;
      }
      .manual-card-top {
        flex-wrap: wrap;
      }
      .manual-sample-headline {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">

        <section class="manual-hero">
          <div class="manual-eyebrow"><i class="bi bi-journal-bookmark"></i> Admin Manual</div>
          <h3>How To Use The Admin Panel</h3>
          <p class="manual-hero-copy">
            This guide explains how the admin panel works, what each module is for, and which workflow to follow when handling bookings, refunds, support, content, users, reports, and utilities.
          </p>
          <div class="manual-hero-meta">
            <div class="manual-meta-pill"><i class="bi bi-grid-1x2"></i> Covers all main sidebar modules</div>
            <div class="manual-meta-pill"><i class="bi bi-people"></i> Notes admin and staff usage</div>
            <div class="manual-meta-pill"><i class="bi bi-search"></i> Searchable inside the page</div>
          </div>
        </section>

        <section class="manual-visual-strip">
          <div class="row g-3">
            <?php foreach ($manual_visuals as $visual): ?>
              <?php renderManualVisual($visual); ?>
            <?php endforeach; ?>
          </div>
        </section>

        <div class="row g-4">
          <div class="col-xl-3">
            <div class="manual-search-card">
              <label for="manualSearch" class="form-label fw-semibold">Search the manual</label>
              <input type="text" id="manualSearch" class="form-control shadow-none" placeholder="Try bookings, refunds, settings, promo...">
              <div class="manual-search-note">Search by module name, page, workflow, or feature.</div>
            </div>

            <aside class="manual-toc">
              <div class="manual-toc-title">Sections</div>
              <?php foreach ($manual_sections as $section): ?>
                <a class="manual-toc-link" href="#<?php echo htmlspecialchars($section['id']); ?>">
                  <span><?php echo htmlspecialchars($section['title']); ?></span>
                  <span><?php echo count($section['cards']); ?> topics</span>
                </a>
              <?php endforeach; ?>
            </aside>
          </div>

          <div class="col-xl-9">
            <div id="manualEmptyState" class="manual-empty d-none">
              No manual topics match your search. Try a broader term like <strong>booking</strong>, <strong>support</strong>, <strong>settings</strong>, or <strong>backup</strong>.
            </div>

            <?php foreach ($manual_sections as $section): ?>
              <section class="manual-section" id="<?php echo htmlspecialchars($section['id']); ?>">
                <div class="manual-section-head">
                  <div>
                    <h4 class="manual-section-title"><?php echo htmlspecialchars($section['title']); ?></h4>
                    <p class="manual-section-copy"><?php echo htmlspecialchars($section['summary']); ?></p>
                  </div>
                  <span class="manual-section-chip"><?php echo count($section['cards']); ?> topics</span>
                </div>

                <div class="row g-3">
                  <?php foreach ($section['cards'] as $card): ?>
                    <?php renderManualCard($card); ?>
                  <?php endforeach; ?>
                </div>
              </section>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script>
    (function () {
      const searchInput = document.getElementById('manualSearch');
      const sections = Array.from(document.querySelectorAll('.manual-section'));
      const cards = Array.from(document.querySelectorAll('.manual-card'));
      const emptyState = document.getElementById('manualEmptyState');

      if (!searchInput) return;

      searchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();

        cards.forEach((card) => {
          const haystack = (card.getAttribute('data-manual-search') || '').toLowerCase();
          const matches = query === '' || haystack.includes(query);
          card.parentElement.classList.toggle('d-none', !matches);
        });

        let visibleSections = 0;
        sections.forEach((section) => {
          const hasVisibleCards = section.querySelectorAll('.col-12.col-md-6:not(.d-none)').length > 0;
          section.classList.toggle('d-none', !hasVisibleCards);
          if (hasVisibleCards) visibleSections++;
        });

        if (emptyState) {
          emptyState.classList.toggle('d-none', visibleSections !== 0);
        }
      });
    })();
  </script>

</body>
</html>
