<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [

            // ─── HOME ────────────────────────────────────────────────────────────
            ['page' => 'home', 'section' => 'hero', 'key' => 'badge_text',       'type' => 'text', 'label' => 'Badge ტექსტი',          'sort_order' => 1,  'value' => 'იურიდიული დახმარების სამსახური'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'title_line1',      'type' => 'text', 'label' => 'სათაური — 1-ლი ხაზი',   'sort_order' => 2,  'value' => 'სამართლებრივი'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'title_accent',     'type' => 'text', 'label' => 'სათაური — accent სიტყვა','sort_order' => 3,  'value' => 'დაცვა'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'title_line2',      'type' => 'text', 'label' => 'სათაური — 2-ე ხაზი',    'sort_order' => 4,  'value' => 'ყველას უფლებაა'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'subtitle',         'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 5,  'value' => 'იურიდიული დახმარების სამსახური 2005 წლიდან უზრუნველყოფს კვალიფიციური სამართლებრივი დახმარების ხელმისაწვდომობას. მართლმსაჯულების სისტემასთან თანასწორი წვდომა — სახელმწიფოს ვალდებულება და ჩვენი მისია.'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'cta_primary',      'type' => 'text', 'label' => 'ღილაკი — მთავარი',       'sort_order' => 6,  'value' => 'მიიღეთ კონსულტაცია'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'cta_secondary',    'type' => 'text', 'label' => 'ღილაკი — მეორადი',       'sort_order' => 7,  'value' => 'სერვისები'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'stat_founded',     'type' => 'text', 'label' => 'სტატ. — დაარსება',        'sort_order' => 8,  'value' => '2005'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'stat_bureaus',     'type' => 'text', 'label' => 'სტატ. — ბიურო',           'sort_order' => 9,  'value' => '13'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'stat_clients',     'type' => 'text', 'label' => 'სტატ. — კლიენტი (წელ.)',  'sort_order' => 10, 'value' => '50,000+'],

            ['page' => 'home', 'section' => 'mission', 'key' => 'section_eyebrow','type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',     'sort_order' => 20, 'value' => 'ჩვენი მისია'],
            ['page' => 'home', 'section' => 'mission', 'key' => 'title',          'type' => 'text', 'label' => 'სათაური',                'sort_order' => 21, 'value' => 'სამართლიანობა ყველასთვის ხელმისაწვდომი'],
            ['page' => 'home', 'section' => 'mission', 'key' => 'body',           'type' => 'html', 'label' => 'ძირითადი ტექსტი',        'sort_order' => 22, 'value' => '<p>სსიპ — იურიდიული დახმარების სამსახური ახორციელებს საქართველოს მოსახლეობის სამართლებრივ დაცვას სახელმწიფო ბიუჯეტის დაფინანსებით. სამსახური უზრუნველყოფს კვალიფიციური სამართლებრივი კონსულტაციისა და იურიდიული დახმარების ხელმისაწვდომობას საქართველოს მთელ ტერიტორიაზე.</p>'],

            ['page' => 'home', 'section' => 'services', 'key' => 'section_eyebrow','type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',   'sort_order' => 30, 'value' => 'სერვისები'],
            ['page' => 'home', 'section' => 'services', 'key' => 'title',          'type' => 'text', 'label' => 'სექციის სათაური',      'sort_order' => 31, 'value' => 'ჩვენი სერვისები'],
            ['page' => 'home', 'section' => 'services', 'key' => 'subtitle',       'type' => 'text', 'label' => 'სექციის ქვე-სათ.',      'sort_order' => 32, 'value' => 'კომპლექსური სამართლებრივი დახმარება ყველა კატეგორიის მოქალაქისთვის'],
            ['page' => 'home', 'section' => 'services', 'key' => 'cta_label',      'type' => 'text', 'label' => 'ყველა სერვისი — ბმ.',   'sort_order' => 33, 'value' => 'ყველა სერვისი'],

            ['page' => 'home', 'section' => 'news',     'key' => 'section_eyebrow','type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',   'sort_order' => 40, 'value' => 'სიახლეები'],
            ['page' => 'home', 'section' => 'news',     'key' => 'title',          'type' => 'text', 'label' => 'სექციის სათაური',      'sort_order' => 41, 'value' => 'უახლესი სიახლეები'],
            ['page' => 'home', 'section' => 'news',     'key' => 'cta_label',      'type' => 'text', 'label' => 'ყველა სიახლე — ბმ.',   'sort_order' => 42, 'value' => 'ყველა სიახლე'],

            // ─── ABOUT ───────────────────────────────────────────────────────────
            ['page' => 'about', 'section' => 'hero', 'key' => 'eyebrow',         'type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',     'sort_order' => 1,  'value' => 'ᲩᲕᲔᲜ ᲨᲔᲡᲐᲮᲔᲑ'],
            ['page' => 'about', 'section' => 'hero', 'key' => 'title',           'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'ᲘᲣᲠᲘᲓᲘᲣᲚᲘ ᲓᲐᲮᲛᲐᲠᲔᲑᲘᲡ ᲡᲐᲛᲡᲐᲮᲣᲠᲘ'],
            ['page' => 'about', 'section' => 'hero', 'key' => 'subtitle',        'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'არის სახელმწიფო ბიუჯეტიდან დაფინანსებული საჯარო სამართლის იურიდიული პირი, რომელიც უზრუნველყოფს იურიდიული კონსულტაციისა და იურიდიული დახმარების ხელმისაწვდომობას საქართველოს მთელ ტერიტორიაზე.'],
            ['page' => 'about', 'section' => 'hero', 'key' => 'independence_note','type' => 'text', 'label' => 'დამოუკიდებლობის შენ.',  'sort_order' => 4,  'value' => 'ᲡᲐᲛᲡᲐᲮᲣᲠᲘ ᲐᲠ ᲔᲥᲕᲔᲛᲓᲔᲑᲐᲠᲔᲑᲐ ᲐᲠᲪᲔᲠᲗ ᲡᲐᲮᲔᲚᲛᲬᲘᲤᲝ ᲝᲠᲒᲐᲜᲝᲡ ᲓᲐ ᲐᲜᲒᲐᲠᲘᲨᲕᲐᲚᲓᲔᲑᲣᲚᲘᲐ ᲛᲮᲝᲚᲝᲓ ᲡᲐᲥᲐᲠᲗᲕᲔᲚᲝᲡ ᲞᲠᲔᲛᲘᲔᲠ-ᲛᲘᲜᲘᲡᲢᲠᲘᲡ ᲬᲘᲜᲐᲨᲔ ᲡᲐᲥᲐᲠᲗᲕᲔᲚᲝᲡ ᲙᲐᲜᲝᲜᲛᲓᲔᲑᲚᲝᲑᲘᲗ ᲓᲐᲓᲒᲔᲜᲘᲚᲘ ᲬᲔᲡᲘᲗ.'],

            ['page' => 'about', 'section' => 'mission', 'key' => 'section_label', 'type' => 'text', 'label' => 'სექციის ეტიქეტი',      'sort_order' => 10, 'value' => 'ᲡᲐᲛᲡᲐᲮᲣᲠᲘᲡ ᲛᲘᲖᲐᲜᲘ'],
            ['page' => 'about', 'section' => 'mission', 'key' => 'body',          'type' => 'html', 'label' => 'მიზნის ტექსტი',          'sort_order' => 11, 'value' => '<p>იურიდიული დახმარების სამსახურის მიზანია სამართლებრივი დაცვისა და სამართლიანი სასამართლოს უფლების განხორციელების ხელშეწყობა, ადამიანის ღირსების, თავისუფლებისა და სხვა ფუნდამენტური უფლებების დაცვა.</p>'],

            ['page' => 'about', 'section' => 'mandate', 'key' => 'section_label', 'type' => 'text', 'label' => 'სექციის ეტიქეტი',      'sort_order' => 20, 'value' => 'ᲡᲐᲛᲡᲐᲮᲣᲠᲘᲡ ᲛᲐᲜᲓᲐᲢᲘ'],
            ['page' => 'about', 'section' => 'mandate', 'key' => 'body',          'type' => 'html', 'label' => 'მანდატის ტექსტი',        'sort_order' => 21, 'value' => '<p>ᲡᲐᲛᲡᲐᲮᲣᲠᲘ ᲣᲖᲠᲣᲜᲕᲔᲚᲧᲝᲤᲡ ᲘᲣᲠᲘᲓᲘᲣᲚ ᲓᲐᲮᲛᲐᲠᲔᲑᲐᲡ ᲡᲘᲡᲮᲚᲘᲡ, ᲡᲐᲛᲝᲥᲐᲚᲐᲥᲝ ᲓᲐ ᲐᲓᲛᲘᲜᲘᲡᲢᲠᲐᲪᲘᲣᲚ ᲡᲐᲥᲛᲔᲔᲑᲖᲔ, ᲐᲡᲔᲕᲔ ᲐᲓᲕᲝᲙᲐᲢᲘᲡ ᲡᲐᲒᲐᲛᲝᲜᲐᲙᲚᲘᲡᲝ ᲬᲔᲡᲘᲗ ᲓᲐᲜᲘᲨᲕᲜᲘᲡ ᲡᲐᲥᲛᲔᲔᲑᲖᲔ.</p>'],

            ['page' => 'about', 'section' => 'principles', 'key' => 'section_label','type' => 'text','label' => 'სექციის ეტიქეტი',    'sort_order' => 30, 'value' => 'ᲡᲐᲛᲡᲐᲮᲣᲠᲘᲡ ᲡᲐᲥᲛᲘᲐᲜᲝᲑᲘᲡ ᲞᲠᲘᲜᲪᲘᲞᲔᲑᲘ'],
            ['page' => 'about', 'section' => 'principles', 'key' => 'principle_1', 'type' => 'text', 'label' => 'პრინციპი 1',            'sort_order' => 31, 'value' => 'სამართლიანობა'],
            ['page' => 'about', 'section' => 'principles', 'key' => 'principle_2', 'type' => 'text', 'label' => 'პრინციპი 2',            'sort_order' => 32, 'value' => 'ხელმისაწვდომობა'],
            ['page' => 'about', 'section' => 'principles', 'key' => 'principle_3', 'type' => 'text', 'label' => 'პრინციპი 3',            'sort_order' => 33, 'value' => 'კვალიფიკაცია'],
            ['page' => 'about', 'section' => 'principles', 'key' => 'principle_4', 'type' => 'text', 'label' => 'პრინციპი 4',            'sort_order' => 34, 'value' => 'დამოუკიდებლობა'],

            ['page' => 'about', 'section' => 'services_reach', 'key' => 'section_label','type' => 'text','label' => 'სექციის ეტიქეტი', 'sort_order' => 40, 'value' => 'ᲡᲐᲛᲡᲐᲮᲣᲠᲘ ᲣᲖᲠᲣᲜᲕᲔᲚᲧᲝᲤᲡ ᲛᲝᲛᲡᲐᲮᲣᲠᲔᲑᲐᲡ'],
            ['page' => 'about', 'section' => 'services_reach', 'key' => 'body',   'type' => 'html', 'label' => 'ტექსტი',                 'sort_order' => 41, 'value' => '<p>ᲡᲐᲛᲡᲐᲮᲣᲠᲘ ᲛᲝᲛᲡᲐᲮᲣᲠᲔᲑᲐᲡ ᲣᲖᲠᲣᲜᲕᲔᲚᲧᲝᲤᲡ ᲑᲘᲣᲠᲝᲔᲑᲘᲡᲐ ᲓᲐ ᲡᲐᲙᲝᲜᲡᲣᲚᲢᲐᲪᲘᲝ ᲪᲔᲜᲢᲠᲔᲑᲘᲡ ᲛᲔᲨᲕᲔᲝᲑᲘᲗ.</p>'],

            // ─── HISTORY ─────────────────────────────────────────────────────────
            ['page' => 'history', 'section' => 'hero', 'key' => 'eyebrow',       'type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',     'sort_order' => 1,  'value' => 'ისტორია'],
            ['page' => 'history', 'section' => 'hero', 'key' => 'title',         'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'სამსახურის ისტორია'],
            ['page' => 'history', 'section' => 'hero', 'key' => 'subtitle',      'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => '2005 წლიდან დღემდე — სამართლებრივი დახმარების განვითარების გზა საქართველოში'],
            ['page' => 'history', 'section' => 'intro', 'key' => 'body',         'type' => 'html', 'label' => 'შესავალი ტექსტი',        'sort_order' => 10, 'value' => '<p>სსიპ „იურიდიული დახმარების სამსახური" დაფუძნდა 2005 წელს, „იურიდიული დახმარების შესახებ" საქართველოს კანონის საფუძველზე. დაარსების დღიდან სამსახური ახორციელებს მოქალაქეთა სამართლებრივი დახმარების უფლების განხორციელებას.</p>'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2005_title', 'type' => 'text', 'label' => '2005 — სათაური',         'sort_order' => 20, 'value' => 'სამსახურის დაარსება'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2005_body',  'type' => 'text', 'label' => '2005 — ტექსტი',          'sort_order' => 21, 'value' => 'მიღებულ იქნა „იურიდიული დახმარების შესახებ" კანონი და დაფუძნდა სამსახური.'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2007_title', 'type' => 'text', 'label' => '2007 — სათაური',         'sort_order' => 22, 'value' => 'ბიუროების ქსელის გაფართოება'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2007_body',  'type' => 'text', 'label' => '2007 — ტექსტი',          'sort_order' => 23, 'value' => 'გაიხსნა ახალი ბიუროები რეგიონებში, სამსახურმა მოიცვა ყველა მხარე.'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2013_title', 'type' => 'text', 'label' => '2013 — სათაური',         'sort_order' => 24, 'value' => 'კანონმდებლობის რეფორმა'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2013_body',  'type' => 'text', 'label' => '2013 — ტექსტი',          'sort_order' => 25, 'value' => 'განხორციელდა სამართლებრივი დახმარების სისტემის კომპლექსური რეფორმა.'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2020_title', 'type' => 'text', 'label' => '2020 — სათაური',         'sort_order' => 26, 'value' => 'ციფრული სერვისების დანერგვა'],
            ['page' => 'history', 'section' => 'timeline', 'key' => '2020_body',  'type' => 'text', 'label' => '2020 — ტექსტი',          'sort_order' => 27, 'value' => 'დაინერგა ონლაინ სერვისები და ელექტრონული განაცხადის სისტემა.'],

            // ─── STRUCTURE ───────────────────────────────────────────────────────
            ['page' => 'structure', 'section' => 'hero', 'key' => 'eyebrow',     'type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',     'sort_order' => 1,  'value' => 'სტრუქტურა'],
            ['page' => 'structure', 'section' => 'hero', 'key' => 'title',       'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'სამსახურის სტრუქტურა'],
            ['page' => 'structure', 'section' => 'hero', 'key' => 'subtitle',    'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'სამსახურის ორგანიზაციული მოწყობა და მართვის სისტემა'],
            ['page' => 'structure', 'section' => 'council', 'key' => 'title',    'type' => 'text', 'label' => 'საბჭო — სათაური',        'sort_order' => 10, 'value' => 'სამეთვალყურეო საბჭო'],
            ['page' => 'structure', 'section' => 'council', 'key' => 'body',     'type' => 'html', 'label' => 'საბჭო — ტექსტი',          'sort_order' => 11, 'value' => '<p>სამეთვალყურეო საბჭო წარმოადგენს სამსახურის მმართველ ორგანოს, რომელიც ზედამხედველობს სამსახურის საქმიანობას და განსაზღვრავს სტრატეგიულ გეგმებს.</p>'],
            ['page' => 'structure', 'section' => 'apparatus', 'key' => 'title',  'type' => 'text', 'label' => 'აპარატი — სათაური',      'sort_order' => 20, 'value' => 'სამსახურის აპარატი'],
            ['page' => 'structure', 'section' => 'apparatus', 'key' => 'body',   'type' => 'html', 'label' => 'აპარატი — ტექსტი',        'sort_order' => 21, 'value' => '<p>სამსახურის აპარატი უზრუნველყოფს სამსახურის ყოველდღიური ოპერაციების წარმართვას და სახელმძღვანელო ფუნქციებს. აპარატი შედგება სხვადასხვა განყოფილებებისგან, რომლებიც ახორციელებენ სამართლებრივ, ადმინისტრაციულ და ლოჯისტიკურ ფუნქციებს.</p>'],
            ['page' => 'structure', 'section' => 'bureaus', 'key' => 'title',    'type' => 'text', 'label' => 'ბიუროები — სათაური',      'sort_order' => 30, 'value' => 'სამსახურის ბიუროები'],
            ['page' => 'structure', 'section' => 'bureaus', 'key' => 'body',     'type' => 'html', 'label' => 'ბიუროები — ტექსტი',        'sort_order' => 31, 'value' => '<p>სამსახური ოპერირებს 13 სამართლებრივი დახმარების ბიუროს, რომლებიც განლაგებულია საქართველოს 9 ადმინისტრაციულ ერთეულში.</p>'],

            // ─── CONTACT ─────────────────────────────────────────────────────────
            ['page' => 'contact', 'section' => 'hero', 'key' => 'eyebrow',       'type' => 'text', 'label' => 'ზედ-სათ. ეტიქეტი',     'sort_order' => 1,  'value' => 'კონტაქტი'],
            ['page' => 'contact', 'section' => 'hero', 'key' => 'title',         'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'დაგვიკავშირდით'],
            ['page' => 'contact', 'section' => 'hero', 'key' => 'subtitle',      'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'ჩვენი გუნდი მზადაა გიპასუხოთ ნებისმიერ კითხვაზე'],
            ['page' => 'contact', 'section' => 'info', 'key' => 'address',       'type' => 'text', 'label' => 'მისამართი',               'sort_order' => 10, 'value' => 'თბილისი, ვაჟა-ფშაველას გამზ. №19'],
            ['page' => 'contact', 'section' => 'info', 'key' => 'phone_1',       'type' => 'text', 'label' => 'ტელეფონი 1',             'sort_order' => 11, 'value' => '+995 (32) 2920055'],
            ['page' => 'contact', 'section' => 'info', 'key' => 'phone_2',       'type' => 'text', 'label' => 'ტელეფონი 2 (უფ. ხაზი)',  'sort_order' => 12, 'value' => '1405'],
            ['page' => 'contact', 'section' => 'info', 'key' => 'email',         'type' => 'text', 'label' => 'ელ-ფოსტა',               'sort_order' => 13, 'value' => 'info@legalaid.ge'],
            ['page' => 'contact', 'section' => 'info', 'key' => 'working_hours', 'type' => 'text', 'label' => 'სამუშაო საათები',         'sort_order' => 14, 'value' => 'ორშ–პარ: 09:00–18:00'],
            ['page' => 'contact', 'section' => 'form', 'key' => 'title',         'type' => 'text', 'label' => 'ფორმის სათაური',          'sort_order' => 20, 'value' => 'გამოგვიგზავნეთ შეტყობინება'],
            ['page' => 'contact', 'section' => 'form', 'key' => 'submit_label',  'type' => 'text', 'label' => 'გაგზავნის ღილაკი',       'sort_order' => 21, 'value' => 'გაგზავნა'],
            ['page' => 'contact', 'section' => 'form', 'key' => 'success_msg',   'type' => 'text', 'label' => 'წარმატების შეტყობინება',  'sort_order' => 22, 'value' => 'თქვენი შეტყობინება წარმატებით გაიგზავნა. მალე დავუკავშირდებით!'],

            // ─── CONSULTATION GUIDE ──────────────────────────────────────────────
            ['page' => 'consultation_guide', 'section' => 'hero', 'key' => 'eyebrow', 'type' => 'text', 'label' => 'ეტიქეტი',          'sort_order' => 1,  'value' => 'კონსულტაციის გიდი'],
            ['page' => 'consultation_guide', 'section' => 'hero', 'key' => 'title',   'type' => 'text', 'label' => 'სათაური',            'sort_order' => 2,  'value' => 'მსურს მივიღო კონსულტაცია'],
            ['page' => 'consultation_guide', 'section' => 'hero', 'key' => 'subtitle','type' => 'text', 'label' => 'ქვე-სათაური',        'sort_order' => 3,  'value' => 'გაიგეთ, სად და როგორ შეგიძლიათ მიიღოთ უფასო სამართლებრივი კონსულტაცია'],
            ['page' => 'consultation_guide', 'section' => 'intro', 'key' => 'title',  'type' => 'text', 'label' => 'შესავ. სათაური',      'sort_order' => 10, 'value' => 'ვინ შეიძლება მიიღოს კონსულტაცია?'],
            ['page' => 'consultation_guide', 'section' => 'intro', 'key' => 'body',   'type' => 'html', 'label' => 'შესავ. ტექსტი',       'sort_order' => 11, 'value' => '<p>უფასო სამართლებრივი კონსულტაცია ხელმისაწვდომია ყველასთვის, ვისაც სჭირდება სამართლებრივი ინფორმაცია ან კერძო სამართლებრივი საკითხების შესახებ. კონსულტაციის მისაღებად არ არის საჭირო სტატუსის დადასტურება.</p>'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'section_title', 'type' => 'text', 'label' => 'ნაბიჯები — სათ.','sort_order' => 20, 'value' => 'კონსულტაციის მიღების ნაბიჯები'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'step_1_title','type' => 'text', 'label' => 'ნაბ. 1 — სათ.',   'sort_order' => 21, 'value' => 'ბიუროს მოძიება'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'step_1_body', 'type' => 'text', 'label' => 'ნაბ. 1 — ტექ.',   'sort_order' => 22, 'value' => 'იპოვეთ თქვენთან ახლომდებარე სამართლებრივი დახმარების ბიური ან საკონსულტაციო ცენტრი.'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'step_2_title','type' => 'text', 'label' => 'ნაბ. 2 — სათ.',   'sort_order' => 23, 'value' => 'ვიზიტი ბიუროში'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'step_2_body', 'type' => 'text', 'label' => 'ნაბ. 2 — ტექ.',   'sort_order' => 24, 'value' => 'გამოცხადეთ პირადობის დამადასტურებელი დოკუმენტით ბიუროში ან საკონსულტაციო ცენტრში.'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'step_3_title','type' => 'text', 'label' => 'ნაბ. 3 — სათ.',   'sort_order' => 25, 'value' => 'კონსულტაცია იურისტთან'],
            ['page' => 'consultation_guide', 'section' => 'steps', 'key' => 'step_3_body', 'type' => 'text', 'label' => 'ნაბ. 3 — ტექ.',   'sort_order' => 26, 'value' => 'კვალიფიციური იურისტი გაგაცნობთ თქვენი სიტუაციის სამართლებრივ შეფასებასა და გამოსავალს.'],

            // ─── PUBLIC INFO ─────────────────────────────────────────────────────
            ['page' => 'public_info', 'section' => 'hero', 'key' => 'eyebrow',   'type' => 'text', 'label' => 'ეტიქეტი',                'sort_order' => 1,  'value' => 'საჯარო ინფორმაცია'],
            ['page' => 'public_info', 'section' => 'hero', 'key' => 'title',     'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'საჯარო ინფორმაცია'],
            ['page' => 'public_info', 'section' => 'hero', 'key' => 'subtitle',  'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'სამსახურის ანგარიშები, ბიუჯეტი, პროქიურმენტი და სხვა საჯარო ინფორმაცია'],
            ['page' => 'public_info', 'section' => 'intro', 'key' => 'body',     'type' => 'html', 'label' => 'შესავ. ტექსტი',          'sort_order' => 10, 'value' => '<p>„საჯარო ინფორმაციის შესახებ" კანონის შესაბამისად, სამსახური ვალდებულია გამოაქვეყნოს კანონმდებლობით განსაზღვრული ინფორმაცია. ქვემოთ მოცემულია ყველა ღია მონაცემი.</p>'],
            ['page' => 'public_info', 'section' => 'categories', 'key' => 'budgets_label',   'type' => 'text', 'label' => 'ბიუჯეტი', 'sort_order' => 20, 'value' => 'ბიუჯეტი და ანგარიშები'],
            ['page' => 'public_info', 'section' => 'categories', 'key' => 'procurement_label','type' => 'text', 'label' => 'შს',       'sort_order' => 21, 'value' => 'სახელმწიფო შეძენები'],
            ['page' => 'public_info', 'section' => 'categories', 'key' => 'vacancies_label', 'type' => 'text', 'label' => 'ვაკ.',     'sort_order' => 22, 'value' => 'ვაკანსიები'],
            ['page' => 'public_info', 'section' => 'categories', 'key' => 'declarations_label','type' => 'text','label' => 'დეკლ.',   'sort_order' => 23, 'value' => 'ქონებრივი დეკლარაციები'],

            // ─── OFFICES ─────────────────────────────────────────────────────────
            ['page' => 'offices', 'section' => 'hero', 'key' => 'eyebrow',       'type' => 'text', 'label' => 'ეტიქეტი',                'sort_order' => 1,  'value' => 'ოფისები'],
            ['page' => 'offices', 'section' => 'hero', 'key' => 'title',         'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'სამსახურის ბიუროები'],
            ['page' => 'offices', 'section' => 'hero', 'key' => 'subtitle',      'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'იურიდიული დახმარება ხელმისაწვდომია საქართველოს მასშტაბით 13 ბიუროს ქსელის მეშვეობით'],
            ['page' => 'offices', 'section' => 'map',  'key' => 'section_title', 'type' => 'text', 'label' => 'რუკის სექ. სათ.',        'sort_order' => 10, 'value' => 'ბიუროების რუკა'],
            ['page' => 'offices', 'section' => 'map',  'key' => 'description',   'type' => 'text', 'label' => 'რუკის აღწერა',            'sort_order' => 11, 'value' => 'დააწკაპუნეთ თქვენს მხარეზე, რომ ნახოთ ახლოს მდებარე ბიუროები'],
            ['page' => 'offices', 'section' => 'list', 'key' => 'section_title', 'type' => 'text', 'label' => 'სიის სექ. სათ.',          'sort_order' => 20, 'value' => 'ყველა ბიუროს სია'],

            // ─── PARALEGAL ───────────────────────────────────────────────────────
            ['page' => 'paralegal', 'section' => 'hero', 'key' => 'eyebrow',     'type' => 'text', 'label' => 'ეტიქეტი',                'sort_order' => 1,  'value' => 'პარალეგალი'],
            ['page' => 'paralegal', 'section' => 'hero', 'key' => 'title',       'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'პარალეგალის სამსახური'],
            ['page' => 'paralegal', 'section' => 'hero', 'key' => 'subtitle',    'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'სამართლებრივი საგანმანათლებლო მომსახურება, ადგილობრივ დონეზე'],
            ['page' => 'paralegal', 'section' => 'intro', 'key' => 'title',      'type' => 'text', 'label' => 'შესავ. სათ.',             'sort_order' => 10, 'value' => 'რა არის პარალეგალური მომსახურება?'],
            ['page' => 'paralegal', 'section' => 'intro', 'key' => 'body',       'type' => 'html', 'label' => 'შესავ. ტექსტი',          'sort_order' => 11, 'value' => '<p>პარალეგალი — ეს არის სპეციალურად მომზადებული პირი, რომელიც აწვდის ადგილობრივ მოსახლეობას პირველადი სამართლებრივი ინფორმაციის გავრცელების, სამართლებრივი ცნობიერების ამაღლებისა და სავარაუდო სამართლებრივი საკითხების გამოვლენის მომსახურებას.</p>'],

            // ─── ARCHIVE ─────────────────────────────────────────────────────────
            ['page' => 'archive', 'section' => 'hero', 'key' => 'eyebrow',       'type' => 'text', 'label' => 'ეტიქეტი',                'sort_order' => 1,  'value' => 'არქივი'],
            ['page' => 'archive', 'section' => 'hero', 'key' => 'title',         'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'სამსახურის არქივი'],
            ['page' => 'archive', 'section' => 'hero', 'key' => 'subtitle',      'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'ისტორიული დოკუმენტები, ანგარიშები და სხვა მასალები'],
            ['page' => 'archive', 'section' => 'intro', 'key' => 'body',         'type' => 'html', 'label' => 'შესავ. ტექსტი',          'sort_order' => 10, 'value' => '<p>ამ გვერდზე განთავსდება იურიდიული დახმარების სამსახურის არქივში დაცული ისტორიული მასალები, ანგარიშები და სხვა კატალოგიზებული დოკუმენტები.</p>'],

            // ─── APPARATUS ───────────────────────────────────────────────────────
            ['page' => 'apparatus', 'section' => 'hero', 'key' => 'eyebrow',     'type' => 'text', 'label' => 'ეტიქეტი',                'sort_order' => 1,  'value' => 'აპარატი'],
            ['page' => 'apparatus', 'section' => 'hero', 'key' => 'title',       'type' => 'text', 'label' => 'სათაური',                'sort_order' => 2,  'value' => 'სამსახურის აპარატი'],
            ['page' => 'apparatus', 'section' => 'hero', 'key' => 'subtitle',    'type' => 'text', 'label' => 'ქვე-სათაური',            'sort_order' => 3,  'value' => 'საქართველოს იურიდიული დახმარების სამსახურის სტრუქტურული ერთეულები'],
            ['page' => 'apparatus', 'section' => 'intro', 'key' => 'body',       'type' => 'html', 'label' => 'შესავ. ტექსტი',          'sort_order' => 10, 'value' => '<p>იურიდიული დახმარების სამსახურის აპარატი მოიცავს პროფესიონალთა გუნდს, რომელიც უზრუნველყოფს სამსახურის საქმიანობის სამართლებრივ, ლოჯისტიკურ და ადმინისტრაციულ უზრუნველყოფას.</p>'],
            ['page' => 'apparatus', 'section' => 'departments', 'key' => 'section_title','type' => 'text','label' => 'განყ. სათ.',       'sort_order' => 20, 'value' => 'სტრუქტურული ერთეულები'],

        ];

        foreach ($blocks as $block) {
            PageContent::updateOrCreate(
                [
                    'page'    => $block['page'],
                    'section' => $block['section'],
                    'key'     => $block['key'],
                ],
                [
                    'value'      => $block['value'],
                    'type'       => $block['type'],
                    'label'      => $block['label'],
                    'sort_order' => $block['sort_order'],
                ]
            );
        }

        $this->command->info('✅ PageContent seeded: ' . count($blocks) . ' blocks across 10 pages.');
    }
}
