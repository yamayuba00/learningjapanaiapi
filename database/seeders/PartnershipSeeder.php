<?php

namespace Database\Seeders;

use App\Models\PartnershipJlptClass;
use App\Models\PartnershipInternship;
use Illuminate\Database\Seeder;

class PartnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed JLPT Classes
        PartnershipJlptClass::create([
            'name' => 'Nihongo Center Jakarta',
            'description' => 'Lembaga kursus bahasa Jepang terpercaya di Jakarta dengan pengajar berpengalaman dan metode pembelajaran yang efektif. Kami telah membantu ribuan siswa lulus ujian JLPT dengan nilai memuaskan.',
            'logo_url' => 'https://example.com/logos/nihongo-center.png',
            'website' => 'https://nihongocenter.co.id',
            'referral_code' => 'NIHONGO2024',
            'programs' => [
                'JLPT N5 Preparation Course',
                'JLPT N4 Preparation Course',
                'JLPT N3 Preparation Course',
                'JLPT N2 Preparation Course',
                'JLPT N1 Preparation Course',
                'Business Japanese',
                'Conversation Class',
            ],
            'contact_whatsapp' => '+6281234567890',
            'contact_instagram' => '@nihongocenter_jkt',
            'is_verified' => true,
            'is_active' => true,
            'display_order' => 1,
        ]);

        PartnershipJlptClass::create([
            'name' => 'Bunka Language School',
            'description' => 'Sekolah bahasa Jepang dengan kurikulum standar Jepang dan fasilitas lengkap. Kami menawarkan kelas reguler, intensif, dan private dengan jadwal fleksibel.',
            'logo_url' => 'https://example.com/logos/bunka.png',
            'website' => 'https://bunkalanguage.com',
            'referral_code' => 'BUNKA2024',
            'programs' => [
                'Regular Class (N5-N1)',
                'Intensive Course',
                'Private Lesson',
                'Online Class',
                'Weekend Class',
            ],
            'contact_whatsapp' => '+6281234567891',
            'contact_instagram' => '@bunka_language',
            'is_verified' => true,
            'is_active' => true,
            'display_order' => 2,
        ]);

        PartnershipJlptClass::create([
            'name' => 'Sakura Japanese Academy',
            'description' => 'Akademi bahasa Jepang dengan fokus pada persiapan JLPT dan budaya Jepang. Metode pembelajaran interaktif dengan native speaker.',
            'logo_url' => 'https://example.com/logos/sakura.png',
            'website' => 'https://sakurajapanese.id',
            'referral_code' => 'SAKURA2024',
            'programs' => [
                'JLPT Preparation (All Levels)',
                'Japanese for Tourism',
                'Japanese for Work',
                'Cultural Workshop',
            ],
            'contact_whatsapp' => '+6281234567892',
            'contact_instagram' => '@sakura_academy',
            'is_verified' => true,
            'is_active' => true,
            'display_order' => 3,
        ]);

        // Seed Internships
        PartnershipInternship::create([
            'name' => 'Tokyo Tech Solutions',
            'description' => 'Perusahaan teknologi Jepang yang menawarkan program magang untuk software engineer dan IT specialist. Kesempatan bekerja dengan teknologi terkini dan tim internasional.',
            'logo_url' => 'https://example.com/logos/tokyo-tech.png',
            'website' => 'https://tokyotech.jp',
            'programs' => [
                'Software Engineer Internship',
                'Data Analyst Internship',
                'UI/UX Designer Internship',
                'Project Manager Trainee',
            ],
            'benefits' => [
                'Gaji kompetitif (¥200,000 - ¥300,000/bulan)',
                'Akomodasi disediakan',
                'Asuransi kesehatan',
                'Pelatihan bahasa Jepang gratis',
                'Sertifikat internasional',
                'Kesempatan kerja full-time',
            ],
            'contact_whatsapp' => '+6281234567893',
            'contact_instagram' => '@tokyotech_careers',
            'is_verified' => true,
            'is_active' => true,
            'total_alumni' => 150,
            'success_rate' => 92.50,
            'display_order' => 1,
        ]);

        PartnershipInternship::create([
            'name' => 'Osaka Hospitality Group',
            'description' => 'Grup hotel dan restoran terkemuka di Osaka yang menawarkan program magang di bidang hospitality dan tourism. Pengalaman kerja di lingkungan internasional.',
            'logo_url' => 'https://example.com/logos/osaka-hospitality.png',
            'website' => 'https://osakahospitality.jp',
            'programs' => [
                'Hotel Management Internship',
                'Restaurant Service Internship',
                'Tourism Guide Trainee',
                'Event Management Internship',
            ],
            'benefits' => [
                'Gaji ¥180,000 - ¥250,000/bulan',
                'Tempat tinggal disediakan',
                'Makan 3x sehari',
                'Pelatihan profesional',
                'Sertifikat hospitality',
                'Networking internasional',
            ],
            'contact_whatsapp' => '+6281234567894',
            'contact_instagram' => '@osaka_hospitality',
            'is_verified' => true,
            'is_active' => true,
            'total_alumni' => 200,
            'success_rate' => 88.75,
            'display_order' => 2,
        ]);

        PartnershipInternship::create([
            'name' => 'Kyoto Manufacturing Corp',
            'description' => 'Perusahaan manufaktur dengan teknologi canggih di Kyoto. Program magang untuk engineering dan production management dengan standar Jepang.',
            'logo_url' => 'https://example.com/logos/kyoto-mfg.png',
            'website' => 'https://kyotomfg.co.jp',
            'programs' => [
                'Mechanical Engineering Internship',
                'Quality Control Trainee',
                'Production Management Internship',
                'Industrial Design Internship',
            ],
            'benefits' => [
                'Gaji ¥220,000 - ¥280,000/bulan',
                'Dormitory tersedia',
                'Transportasi disediakan',
                'Pelatihan teknis intensif',
                'Sertifikat engineering',
                'Peluang karir jangka panjang',
            ],
            'contact_whatsapp' => '+6281234567895',
            'contact_instagram' => '@kyoto_manufacturing',
            'is_verified' => true,
            'is_active' => true,
            'total_alumni' => 120,
            'success_rate' => 95.00,
            'display_order' => 3,
        ]);

        $this->command->info('Partnership data seeded successfully!');
    }
}
