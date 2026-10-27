CREATE DATABASE IF NOT EXISTS `taajir_makifat` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `taajir_makifat`;

CREATE TABLE `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `settings` (
  `setting_key` VARCHAR(100) PRIMARY KEY,
  `setting_value` LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `icon_svg` TEXT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `why_us` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `icon_svg` TEXT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `process_steps` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `step_number` VARCHAR(10) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `image_path` VARCHAR(500) NOT NULL,
  `alt_text` VARCHAR(255),
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `faqs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(500) NOT NULL,
  `answer` TEXT NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `intro_paragraphs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content` TEXT NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `occasions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `city_seo` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==================== SEED SETTINGS ====================
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'تأجير مكيفات'),
('site_title', 'تأجير مكيفات | تأجير بيوت شعر وخيم شعبية وكراسي ملكية ودفايات ومكيفات سبلت في الرياض وكل مناطق المملكة'),
('site_description', 'تأجير مكيفات: تأجير بيوت شعر، خيم شعبية، كراسي ملكية، دفايات ومكيفات سبلت للأفراح والعزاء والتجمعات والمخيمات والمناسبات في الرياض وجدة والدمام ومكة والمدينة وجميع مناطق المملكة. تركيب وتفكيك وأسعار مناسبة. تواصل: 0503513824'),
('site_keywords', 'تأجير مكيفات, تأجير مكيفات الرياض, تأجير مكيفات سبلت, تأجير مكيفات للمناسبات, تأجير بيوت شعر, تأجير خيم شعبية, تأجير كراسي ملكية, تأجير دفايات, مكيفات سبلت للايجار, تجهيز مناسبات, تجهيز أفراح, تجهيز عزاء, تجهيز مخيمات, تأجير مكيفات جدة, تأجير مكيفات مكة, تأجير مكيفات الدمام'),
('phone_display', '0503 513 824'),
('phone_tel', '+966503513824'),
('whatsapp', '966503513824'),
('hero_badge', 'نخدم جميع مناطق المملكة — مقرنا الرياض'),
('hero_line1', 'بيت شعر ومكيف'),
('hero_line2', 'جاهزين <em>لمناسبتك</em>'),
('hero_sub', 'تأجير مكيفات لتأجير بيوت الشعر والخيم الشعبية والكراسي الملكية والدفايات ومكيفات السبلت لكل أنواع المناسبات — أفراح، عزاء، تجمعات عائلية، مخيمات واستراحات، حفلات تخرج وليالي رمضانية.\nنجهز لك البيت والتبريد والتدفئة والجلسات ونوصل لأي مدينة في المملكة، بتركيب وتفكيك وأسعار واضحة.'),
('coverage_title', 'نطاق التغطية'),
('coverage_sub', 'مقرنا الأساسي الرياض، ونوصل لأي منطقة في المملكة — اللي بيتصل بينا نروحله.'),
('contact_note', 'مقرنا الأساسي الرياض، ونخدم جميع مناطق المملكة العربية السعودية.'),
('cta_title', 'عندك مناسبة قريبة؟'),
('cta_text', 'اتصل بينا دلوقتي ونجهزلك بيت الشعر والتبريد المناسب من غير أي التزام.'),
('footer_about', 'تأجير بيوت شعر وخيم شعبية وكراسي ملكية ودفايات ومكيفات سبلت ومجالس ومظلات لكل المناسبات — أفراح، عزاء، تجمعات، مخيمات واستراحات. مقرنا الرياض، ونخدم جميع مناطق المملكة.'),
('footer_copyright', '2026'),
('schema_description', 'تأجير بيوت شعر، خيم شعبية، كراسي ملكية، دفايات ومكيفات سبلت للأفراح والعزاء والتجمعات والمخيمات'),
('og_title', 'تأجير مكيفات | بيوت شعر وخيم شعبية وكراسي ملكية ودفايات'),
('og_description', 'تأجير بيوت شعر وخيم شعبية وكراسي ملكية ودفايات ومكيفات سبلت لكل المناسبات في جميع مناطق المملكة. مقرنا الرياض.');

-- ==================== SEED SERVICES ====================
INSERT INTO `services` (`icon_svg`, `title`, `description`, `sort_order`) VALUES
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M6 38 L24 8 L42 38 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /><path d="M24 38 L24 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" /><path d="M14 38 L34 38" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.5" /></svg>', 'تأجير بيوت شعر', 'تأجير بيوت شعر وخيم شعبية بأحجام مختلفة تناسب الأفراح والعزاء والتجمعات العائلية والمخيمات، مع تركيب وترتيب كامل قبل المناسبة. بيوت شعر مكيفة وفخمة وملكية وعصرية للايجار في الرياض وجميع مناطق المملكة.', 1),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><rect x="8" y="14" width="32" height="11" rx="3" fill="none" stroke="currentColor" stroke-width="2.2" /><path d="M12 30 C16 26 20 34 24 30 C28 26 32 34 36 30" stroke="currentColor" stroke-width="2.1" fill="none" stroke-linecap="round" /></svg>', 'تأجير مكيفات سبلت', 'تأجير مكيفات سبلت وتبريد للمناسبات بسعات مناسبة لتبريد بيت الشعر والخيمة بالكامل، مع دفايات للشتاء وتوصيل وتركيب سريع قبل موعد المناسبة. ايجار مكيفات بالساعة أو باليوم أو بالشهر.', 2),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M8 30 C8 30 16 38 24 38 C32 38 40 30 40 30" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /><path d="M12 30 L12 16 M36 30 L36 16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /><path d="M12 16 C12 12 16 10 24 10 C32 10 36 12 36 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></svg>', 'تجهيز متكامل للمناسبة', 'بيت شعر وتبريد وتدفئة وكراسي ملكية مع بعض بترتيب موحّد، ينفعك سواء كانت المناسبة فرح أو عزاء أو تجمع عائلي أو مخيم أو ليالي رمضانية أو حفلة تخرج.', 3),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><circle cx="24" cy="24" r="16" fill="none" stroke="currentColor" stroke-width="2.2" /><path d="M24 14 L24 24 L32 28" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none" /></svg>', 'تركيب وتفكيك سريع', 'نجهز بيت الشعر والخيم والمكيفات والدفايات والكراسي قبل المناسبة بوقت كافٍ، ونفكك كل شيء بعد الانتهاء من غير ما يشغل بالك.', 4),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M6 38 L24 8 L42 38 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /><circle cx="24" cy="30" r="3" fill="none" stroke="currentColor" stroke-width="2" /></svg>', 'تأجير خيم شعبية', 'خيم شعبية للايجار بتصاميم تراثية أصيلة تناسب الأفراح والمناسبات والمخيمات والرحلات، بأحجام صغيرة وكبيرة وخيام ملكية للمناسبات الكبيرة، وتتجهز بالفرش والإضاءة والتبريد والتدفئة.', 5),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M14 8 L14 24 L34 24 L34 8 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /><path d="M12 24 L12 40 M36 24 L36 40 M12 32 L36 32" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></svg>', 'تأجير كراسي ملكية', 'كراسي ملكية للايجار وكراسي كوشة فخمة وكراسي وطاولات للأفراح والمناسبات، بتعطي مناسبتك شكل راقي وتريح ضيوفك طول الحفلة، وبأعداد كبيرة تناسب القاعات والخيام وبيوت الشعر.', 6),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M24 6 C24 6 14 18 14 28 a10 10 0 0 0 20 0 C34 18 24 6 24 6 Z" fill="none" stroke="currentColor" stroke-width="2.2" /><path d="M24 22 C24 22 20 27 20 30 a4 4 0 0 0 8 0 C28 27 24 22 24 22 Z" fill="none" stroke="currentColor" stroke-width="2" /></svg>', 'تأجير دفايات', 'دفايات للايجار غاز وكهرباء وخارجية لتدفئة بيوت الشعر والخيام والمخيمات والمناسبات في الشتاء، بتوصيل وتركيب وتشغيل آمن وبعدد يكفي مساحة المكان وعدد الحضور.', 7),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M8 36 L8 22 C8 14 16 10 24 10 C32 10 40 14 40 22 L40 36" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /><path d="M8 36 L40 36" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></svg>', 'تأجير مجالس وفرش', 'مجالس عربية للايجار وفرش وجلسات أرضية ومساند مريحة بتصاميم تراثية وعصرية، تكمل تجهيز بيت الشعر أو الخيمة وتخلي الضيوف مرتاحين في الأفراح والعزاء والتجمعات.', 8),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M6 26 C6 16 14 10 24 10 C34 10 42 16 42 26 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /><path d="M24 26 L24 40" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></svg>', 'تأجير مظلات وطاولات', 'مظلات وخيام للايجار وطاولات بأحجام مختلفة، مناسبة للمناسبات الخارجية والحدائق والاستراحات والمزارع، مع إضاءة مناسبات وتجهيزات تكمل المكان بشكل متكامل.', 9),
('<svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><path d="M30 8 a14 14 0 1 0 10 24 A12 12 0 0 1 30 8 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /></svg>', 'تجهيز خيام رمضانية', 'خيام رمضانية وغبقات وافطارات جماعية، نجهز الخيمة بالتبريد والكراسي والفرش والإضاءة عشان ليالي رمضان تكون مريحة وجميلة لأهلك وضيوفك في أي مدينة بالمملكة.', 10);

-- ==================== SEED WHY US ====================
INSERT INTO `why_us` (`icon_svg`, `title`, `description`, `sort_order`) VALUES
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><path d="M24 4 C24 4 11 20 11 29 a13 13 0 0 0 26 0 C37 20 24 4 24 4 Z" fill="none" stroke="currentColor" stroke-width="2.2" /></svg>', 'تغطية كل مناطق المملكة', 'مقرنا الرياض، وفريقنا يتحرك لأي مدينة أو قرية تحتاجنا فيها مناسبتك، من جدة ومكة والمدينة للدمام والقصيم وتبوك وأبها.', 1),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><path d="M24 6 L40 12 L40 24 C40 34 33 41 24 44 C15 41 8 34 8 24 L8 12 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /><path d="M17 24 L22 29 L31 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg>', 'تجهيز موثوق', 'بيوت شعر ومكيفات نظيفة وجاهزة، نركبها بعناية قبل موعد مناسبتك بوقت كافٍ.', 2),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><rect x="8" y="14" width="32" height="11" rx="3" fill="none" stroke="currentColor" stroke-width="2.2" /><path d="M12 30 C16 26 20 34 24 30 C28 26 32 34 36 30" stroke="currentColor" stroke-width="2.1" fill="none" stroke-linecap="round" /></svg>', 'تبريد يكفي عدد الحضور', 'نحدد سعة التكييف المناسبة لحجم بيت الشعر وعدد الضيوف المتوقع.', 3),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><path d="M10 10 L26 10 L38 22 L22 38 L10 26 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /><circle cx="17" cy="17" r="2.4" fill="currentColor" /></svg>', 'أسعار واضحة', 'نتفق على السعر حسب المقاس والمدة قبل التنفيذ، من غير أي مفاجآت. أسعار تأجير مكيفات وبيوت شعر وخيام مناسبة للجميع.', 4),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><circle cx="24" cy="24" r="16" fill="none" stroke="currentColor" stroke-width="2.2" /><path d="M17 24 L22 29 L31 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg>', 'تجهيز لكل الفصول', 'مكيفات وتبريد في الصيف، ودفايات وتدفئة في الشتاء، فمناسبتك تكون مريحة في أي وقت من السنة.', 5),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><circle cx="24" cy="24" r="16" fill="none" stroke="currentColor" stroke-width="2.2" /><path d="M24 14 L24 24 L32 28" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none" /></svg>', 'التزام بالمواعيد', 'نوصل ونركب في الميعاد المتفق عليه قبل بداية المناسبة، ونفكك بعد الانتهاء بدون تأخير.', 6),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><path d="M6 38 L24 8 L42 38 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /></svg>', 'تشكيلة وأحجام متنوعة', 'بيوت شعر وخيم شعبية وكراسي ملكية بأشكال وأحجام تناسب المناسبات الصغيرة والكبيرة.', 7),
('<svg viewBox="0 0 48 48" width="28" height="28" aria-hidden="true"><path d="M24 6 L40 12 L40 24 C40 34 33 41 24 44 C15 41 8 34 8 24 L8 12 Z" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" /></svg>', 'خدمة متكاملة من مكان واحد', 'بيت شعر ومكيف ودفاية وكراسي ومجالس ومظلات كلها عندنا، فمش هتحتاج تدور على أكتر من مورد.', 8);

-- ==================== SEED PROCESS ====================
INSERT INTO `process_steps` (`step_number`, `title`, `description`, `sort_order`) VALUES
('01', 'التواصل والتفاصيل', 'تكلمنا وتوضح نوع المناسبة، المكان، وعدد الحضور المتوقع.', 1),
('02', 'تحديد المقاس والسعة', 'نحدد حجم بيت الشعر وعدد المكيفات المناسب، ونتفق على السعر.', 2),
('03', 'التركيب قبل الموعد', 'نوصل ونركب بيت الشعر والمكيفات بوقت كافٍ قبل بداية مناسبتك.', 3),
('04', 'التفكيك بعد الانتهاء', 'فريقنا يرجع يفكك كل شيء بعد ما تخلص المناسبة من غير ما يشغل بالك.', 4);

-- ==================== SEED CITIES ====================
INSERT INTO `cities` (`name`, `sort_order`) VALUES
('الرياض',1),('جدة',2),('مكة المكرمة',3),('المدينة المنورة',4),
('الدمام',5),('الخبر',6),('القصيم',7),('تبوك',8),
('حائل',9),('أبها',10),('جازان',11),('نجران',12),
('الطائف',13),('الأحساء',14),('ينبع',15),('الجبيل',16),
('الخرج',17),('بريدة',18),('عنيزة',19),('خميس مشيط',20),
('الباحة',21),('الجوف',22),('عرعر',23),('الدرعية',24);

-- ==================== SEED GALLERY ====================
INSERT INTO `gallery` (`image_path`, `alt_text`, `sort_order`) VALUES
('imgs/img1.jpeg','تأجير بيوت شعر للأفراح والمناسبات في الرياض',1),
('imgs/img2.jpeg','تأجير مكيفات سبلت لبيوت الشعر والخيام',2),
('imgs/img3.jpeg','خيم شعبية للايجار مجهزة بالكامل',3),
('imgs/img4.jpeg','كراسي ملكية للايجار للأفراح والمناسبات',4),
('imgs/img5.jpeg','دفايات للايجار لبيوت الشعر والمخيمات',5),
('imgs/img6.jpeg','بيت شعر مكيف لمناسبات العزاء',6),
('imgs/img7.jpeg','تجهيز بيوت شعر وخيام للتجمعات العائلية',7),
('imgs/img8.jpeg','تأجير خيام ومظلات للمناسبات في المملكة',8),
('imgs/img9.jpeg','بيت شعر ملكي فخم للايجار',9),
('imgs/img10.jpeg','تأجير مكيفات خارجية للمناسبات',10),
('imgs/img11.jpeg','تركيب بيوت شعر ومكيفات في الرياض',11),
('imgs/img12.jpeg','خيمة شعبية مع كراسي ملكية ودفايات',12),
('imgs/img13.jpeg','تأجير مكيفات وبيوت شعر في جدة والدمام',13),
('imgs/img14.jpeg','تجهيز مخيمات واستراحات ومزارع',14),
('imgs/img15.jpeg','بيوت شعر عصرية مجهزة بالتبريد والتدفئة',15),
('imgs/img16.jpeg','جلسات ملكية وكراسي فخمة للايجار',16),
('imgs/img17.jpeg','شركة تأجير مكيفات وبيوت شعر وخيم في السعودية',17);

-- ==================== SEED INTRO ====================
INSERT INTO `intro_paragraphs` (`content`, `sort_order`) VALUES
('كل مناسبة ليها احتياجها: فرح محتاج بيت شعر أو خيمة شعبية واسعة وكراسي ملكية فخمة وتبريد يكفي عدد الحضور، وعزاء محتاج تجهيز سريع ومريح لاستقبال المعزين، وتجمع عائلي أو مخيم محتاج مكان بسيط ومكيف في الصيف ودفايات في الشتاء. في <strong>تأجير مكيفات</strong> بنجهزلك <strong>بيت الشعر</strong> المناسب مع <strong>مكيفات سبلت</strong> تبرد المكان بالكامل، و<strong>دفايات</strong> للتدفئة، و<strong>كراسي ملكية</strong> للضيوف، و<strong>خيم شعبية</strong> بأشكال وأحجام مختلفة، من التركيب قبل المناسبة لحد التفكيك بعد ما تخلص، وده في أي مدينة بالمملكة: الرياض، جدة، مكة، المدينة، الدمام، الخبر، القصيم، تبوك، أبها وغيرها — مقرنا الأساسي الرياض لكن فريقنا يتحرك أينما احتجتوه.', 1),
('لو بتدور على <strong>تأجير بيوت شعر في الرياض</strong> أو <strong>ايجار خيام للمناسبات</strong> أو <strong>مكيفات للايجار</strong> بسعر مناسب، فانت في المكان الصح. بنوفرلك <strong>بيوت شعر مكيفة</strong> و<strong>بيوت شعر فخمة وملكية وعصرية</strong>، و<strong>خيام شعبية للايجار</strong> مجهزة بالكامل، مع <strong>مكيفات سبلت وصحراوية ومتنقلة</strong> و<strong>دفايات غاز وكهرباء</strong> تناسب كل الفصول، وكمان <strong>كراسي ملكية وكراسي كوشة وطاولات ومجالس عربية</strong> تكمل جمال مناسبتك.', 2),
('احنا مش بس بنأجر قطع، احنا بنجهز لك مناسبة متكاملة: <strong>تجهيز أفراح</strong>، <strong>تجهيز بيوت عزاء</strong>، <strong>تجهيز تجمعات عائلية</strong>، <strong>تجهيز مخيمات واستراحات ومزارع</strong>، <strong>تجهيز حفلات تخرج</strong>، و<strong>تجهيز خيام رمضانية وغبقات وافطارات</strong>، بخدمة سريعة وتوصيل وتركيب وتفكيك، وأسعار نتفق عليها من الأول من غير أي مفاجآت.', 3);

-- ==================== SEED OCCASIONS ====================
INSERT INTO `occasions` (`title`, `content`, `sort_order`) VALUES
('تأجير بيوت شعر ومكيفات للأفراح', 'الفرح له رونقه، وبيت الشعر أو الخيمة الشعبية بتدي المناسبة طابع أصيل وفخم. بنجهز لك بيت شعر واسع بمكيفات سبلت تكفي المدعوين، وكراسي ملكية وكوشة وطاولات ومجالس عربية، مع إضاءة مناسبات تناسب الليلة، ودفايات لو الفرح في الشتاء. خدمة تجهيز أفراح وحفلات زواج في الرياض وجدة والدمام وكل مدن المملكة.', 1),
('تأجير بيوت شعر وخيام للعزاء', 'في أوقات العزاء بنقدر ظروفكم، ونجهز بيت عزاء مريح ومكيف بسرعة وبهدوء لاستقبال المعزين، بكراسي ومجالس وتبريد مناسب، مع تركيب سريع وتفكيك بعد انتهاء أيام العزاء. تجهيز بيوت عزاء بأسعار مناسبة وبأعلى درجات الاحترام والالتزام بالمواعيد.', 2),
('تأجير بيوت شعر وخيم للتجمعات العائلية والمخيمات', 'لو عندك تجمع عائلي أو مخيم أو طلعة برية أو استراحة أو مزرعة، بنوفر لك خيمة شعبية أو بيت شعر بتبريد في الصيف وتدفئة في الشتاء، مع جلسات مريحة وفرش ومظلات. تجهيز مخيمات واستراحات ومزارع بكل ما تحتاجه من مستلزمات، وبتوصيل لحد موقعك.', 3),
('تجهيز خيام رمضانية وغبقات وافطارات', 'في رمضان بنجهز خيام وبيوت شعر للغبقات والافطارات الجماعية والليالي الرمضانية، بتبريد قوي وكراسي مريحة وإضاءة تناسب الأجواء الروحانية، سواء للعائلة أو للشركات أو للجمعيات الخيرية.', 4),
('تجهيز حفلات التخرج والمناسبات الخاصة', 'حفلة تخرج، عيد ميلاد، خطوبة، ملكة، استقبال مولود أو أي مناسبة خاصة، بنجهز لك المكان بكراسي ملكية وطاولات وتبريد وتدفئة بحسب الموسم، عشان تقضي وقت جميل مع أحبابك.', 5);

-- ==================== SEED CITY SEO ====================
INSERT INTO `city_seo` (`title`, `content`, `sort_order`) VALUES
('تأجير مكيفات وبيوت شعر في الرياض', 'مقرنا الأساسي في الرياض، فنوصل لكل أحياء الرياض والدرعية والخرج والمزاحمية والدوادمي بسرعة، ونجهز بيوت الشعر والخيم الشعبية والمكيفات والدفايات والكراسي الملكية لجميع المناسبات.', 1),
('تأجير مكيفات وخيام في جدة ومكة والمدينة والطائف', 'نخدم المنطقة الغربية كاملة، من جدة ومكة المكرمة والمدينة المنورة والطائف وينبع، بتجهيز بيوت شعر ومكيفات سبلت ودفايات وكراسي ملكية للأفراح والعزاء والتجمعات.', 2),
('تأجير بيوت شعر ومكيفات في الدمام والخبر والأحساء والجبيل', 'نغطي المنطقة الشرقية بالكامل، الدمام والخبر والظهران والأحساء والجبيل، بخدمة تركيب وتفكيك وتبريد يناسب أجواء المنطقة.', 3),
('تأجير خيم شعبية ومكيفات في القصيم وحائل وتبوك والجوف', 'بريدة وعنيزة وحائل وتبوك والجوف وعرعر وسكاكا، كل مدن الشمال والوسط نوصلها بتجهيزات كاملة للمناسبات والمخيمات والمزارع والاستراحات.', 4),
('تأجير بيوت شعر ودفايات في أبها وجازان ونجران والباحة', 'في الجنوب كمان بنوفر بيوت شعر وخيام ومكيفات ودفايات، خصوصًا في أجواء أبها وخميس مشيط والباحة اللي بتحتاج تدفئة في أغلب أيام السنة.', 5);

-- ==================== SEED FAQS ====================
INSERT INTO `faqs` (`question`, `answer`, `sort_order`) VALUES
('التأجير يشمل التركيب والتفكيك؟', 'أيوه، نوصل ونركب بيت الشعر والمكيفات قبل المناسبة، ونرجع نفكك كل شيء بعد الانتهاء.', 1),
('المكيفات بتكفي تبرد بيت الشعر بالكامل؟', 'نحدد عدد وسعة المكيفات المناسبة حسب حجم بيت الشعر وعدد الحضور المتوقع، عشان التبريد يكفي.', 2),
('هل تخدمون خارج الرياض؟', 'أيوه، مقرنا الأساسي الرياض بس نخدم جميع مناطق المملكة، اللي بيتصل بينا نروحله.', 3),
('قد إيش محتاجين وقت قبل المناسبة؟', 'كل ما كان التواصل أبكر كل ما كان أحسن، وننسق معاك موعد التركيب بما يناسب مناسبتك.', 4),
('بيوت الشعر تناسب الأفراح والعزاء؟', 'أيوه، نجهزها بترتيب يناسب نوع المناسبة سواء كانت فرح أو عزاء أو تجمع عائلي.', 5),
('هل عندكم خيم شعبية للايجار؟', 'أيوه، عندنا خيم شعبية وخيام ملكية بأحجام مختلفة للأفراح والعزاء والمخيمات والتجمعات، وتتجهز بالتبريد والتدفئة والفرش والكراسي.', 6),
('هل توفرون كراسي ملكية للايجار؟', 'أيوه، نوفر كراسي ملكية وكراسي كوشة وكراسي وطاولات بأعداد كبيرة تناسب الأفراح والحفلات والمناسبات.', 7),
('هل تأجرون دفايات للشتاء؟', 'أيوه، عندنا دفايات غاز وكهرباء وخارجية لتدفئة بيوت الشعر والخيام والمخيمات، ونحدد العدد المناسب حسب مساحة المكان.', 8),
('هل ممكن أأجر مكيفات بس من غير بيت شعر؟', 'طبعًا، تقدر تأجر مكيفات سبلت لوحدها أو بيت شعر لوحده أو خيمة أو كراسي أو دفايات، أو تاخد تجهيز متكامل حسب احتياجك.', 9),
('كام سعر تأجير بيت الشعر والمكيفات؟', 'السعر بيختلف حسب مقاس بيت الشعر وعدد المكيفات ومدة التأجير والمدينة، وبنتفق معاك عليه بوضوح قبل التنفيذ. تواصل معانا على 0503513824 وهنديك عرض سعر مناسب.', 10),
('هل التأجير بالساعة أو باليوم؟', 'نأجر حسب احتياجك، لليلة أو لعدة أيام أو لفترة أطول، وننسق معاك المدة المناسبة للمناسبة أو المخيم.', 11),
('هل تجهزون بيوت عزاء؟', 'أيوه، نجهز بيوت عزاء بسرعة وبترتيب مريح لاستقبال المعزين، بمكيفات وكراسي ومجالس، مع تركيب سريع وتفكيك بعد انتهاء العزاء.', 12),
('هل تجهزون المخيمات والاستراحات والمزارع؟', 'أيوه، نوصل لأي مخيم أو استراحة أو مزرعة ونجهز الخيمة أو بيت الشعر بالتبريد والتدفئة والجلسات.', 13),
('هل تجهزون خيام رمضانية وغبقات؟', 'أيوه، نجهز خيام وبيوت شعر للغبقات والافطارات الجماعية والليالي الرمضانية، بتبريد وكراسي وفرش وإضاءة مناسبة.', 14),
('إزاي أحجز وأتواصل معاكم؟', 'تقدر تتصل بينا على 0503513824 أو تراسلنا واتساب أو تملأ فورم الحجز في الموقع، وهنرد عليك في أسرع وقت.', 15);