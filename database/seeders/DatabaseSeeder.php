<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PipelineStage;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Activity;
use App\Models\Task;
use App\Models\Meeting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        $admin = User::create([
            'name' => 'Admin CRM',
            'email' => 'admin@webcare.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'position' => 'Administrator',
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Budi Manager',
            'email' => 'manager@webcare.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '082345678901',
            'position' => 'Sales Manager',
            'is_active' => true,
        ]);

        $sales1 = User::create([
            'name' => 'Ardi Sales',
            'email' => 'sales@webcare.com',
            'password' => Hash::make('password'),
            'role' => 'sales',
            'phone' => '083456789012',
            'position' => 'Sales Executive',
            'is_active' => true,
        ]);

        // Create pipeline stages
        $stages = [
            ['name' => 'Lead Masuk', 'color' => '#6366f1', 'order' => 1, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Dihubungi', 'color' => '#8b5cf6', 'order' => 2, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Kualifikasi', 'color' => '#f59e0b', 'order' => 3, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Proposal', 'color' => '#3b82f6', 'order' => 4, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Negosiasi', 'color' => '#0ea5e9', 'order' => 5, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Deal Menang', 'color' => '#10b981', 'order' => 6, 'is_won' => true, 'is_lost' => false],
            ['name' => 'Deal Gagal', 'color' => '#ef4444', 'order' => 7, 'is_won' => false, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            PipelineStage::create($stage);
        }

        $stage1 = PipelineStage::where('order', 1)->first();
        $stage2 = PipelineStage::where('order', 2)->first();
        $stage3 = PipelineStage::where('order', 3)->first();
        $stage4 = PipelineStage::where('order', 4)->first();
        $stage5 = PipelineStage::where('order', 5)->first();

        // Create companies
        $companies = [
            Company::create(['name' => 'PT Maju Bersama', 'industry' => 'Teknologi', 'phone' => '0215550001', 'email' => 'info@majubersama.co.id', 'city' => 'Jakarta', 'employees' => 150]),
            Company::create(['name' => 'CV Sukses Mandiri', 'industry' => 'Retail', 'phone' => '0215550002', 'email' => 'cs@suksesmandiri.com', 'city' => 'Surabaya', 'employees' => 50]),
            Company::create(['name' => 'PT Digital Nusantara', 'industry' => 'Digital Marketing', 'phone' => '0215550003', 'email' => 'hello@digitalnusantara.id', 'city' => 'Bandung', 'employees' => 80]),
        ];

        // Create sample leads
        $leadData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com', 'phone' => '081111111111', 'whatsapp' => '081111111111', 'company' => 'PT Maju Bersama', 'status' => 'new', 'priority' => 'high', 'source' => 'website', 'estimated_value' => 50000000, 'pipeline_stage_id' => $stage1->id, 'assigned_to' => $sales1->id],
            ['name' => 'Siti Rahayu', 'email' => 'siti@example.com', 'phone' => '082222222222', 'whatsapp' => '082222222222', 'company' => 'CV Sukses', 'status' => 'contacted', 'priority' => 'medium', 'source' => 'referral', 'estimated_value' => 25000000, 'pipeline_stage_id' => $stage2->id, 'assigned_to' => $sales1->id],
            ['name' => 'Bintang Pratama', 'email' => 'bintang@example.com', 'phone' => '083333333333', 'whatsapp' => '083333333333', 'company' => 'PT Digital', 'status' => 'qualified', 'priority' => 'high', 'source' => 'campaign', 'estimated_value' => 75000000, 'pipeline_stage_id' => $stage3->id, 'assigned_to' => $manager->id],
            ['name' => 'Dewi Kusuma', 'email' => 'dewi@example.com', 'phone' => '084444444444', 'whatsapp' => '084444444444', 'company' => 'PT Kreatif', 'status' => 'proposal', 'priority' => 'high', 'source' => 'whatsapp', 'estimated_value' => 120000000, 'pipeline_stage_id' => $stage4->id, 'assigned_to' => $manager->id],
            ['name' => 'Rizky Hidayat', 'email' => 'rizky@example.com', 'phone' => '085555555555', 'whatsapp' => '085555555555', 'company' => 'CV Abadi', 'status' => 'negotiation', 'priority' => 'urgent', 'source' => 'email', 'estimated_value' => 200000000, 'pipeline_stage_id' => $stage5->id, 'assigned_to' => $sales1->id],
            ['name' => 'Indah Permata', 'email' => 'indah@example.com', 'phone' => '086666666666', 'whatsapp' => '086666666666', 'company' => 'PT Permata', 'status' => 'won', 'priority' => 'high', 'source' => 'website', 'estimated_value' => 85000000, 'pipeline_stage_id' => $stage5->id, 'assigned_to' => $sales1->id],
            ['name' => 'Eko Santoso', 'email' => 'eko@example.com', 'phone' => '087777777777', 'whatsapp' => '087777777777', 'company' => 'CV Santoso', 'status' => 'new', 'priority' => 'low', 'source' => 'manual', 'estimated_value' => 15000000, 'pipeline_stage_id' => $stage1->id, 'assigned_to' => $sales1->id],
            ['name' => 'Maya Putri', 'email' => 'maya@example.com', 'phone' => '088888888888', 'whatsapp' => '088888888888', 'company' => 'PT Maya Corp', 'status' => 'contacted', 'priority' => 'medium', 'source' => 'campaign', 'estimated_value' => 45000000, 'pipeline_stage_id' => $stage2->id, 'assigned_to' => $manager->id],
        ];

        $leads = [];
        foreach ($leadData as $data) {
            $leads[] = Lead::create(array_merge($data, ['last_contacted_at' => now()->subDays(rand(0, 10))]));
        }

        // Create deals
        $dealStages = PipelineStage::all();
        $deal1 = Deal::create([
            'title' => 'Project Website PT Maju Bersama',
            'lead_id' => $leads[2]->id,
            'pipeline_stage_id' => $stage4->id,
            'assigned_to' => $manager->id,
            'value' => 75000000,
            'probability' => 70,
            'status' => 'open',
            'expected_close_date' => now()->addDays(14),
        ]);

        $deal2 = Deal::create([
            'title' => 'Sistem ERP CV Sukses Mandiri',
            'lead_id' => $leads[4]->id,
            'pipeline_stage_id' => $stage5->id,
            'assigned_to' => $sales1->id,
            'value' => 200000000,
            'probability' => 85,
            'status' => 'open',
            'expected_close_date' => now()->addDays(7),
        ]);

        $deal3 = Deal::create([
            'title' => 'Digital Marketing Package',
            'lead_id' => $leads[5]->id,
            'pipeline_stage_id' => PipelineStage::where('is_won', true)->first()->id,
            'assigned_to' => $sales1->id,
            'value' => 85000000,
            'probability' => 100,
            'status' => 'won',
            'closed_date' => now()->subDays(5),
        ]);

        // Create activities
        $activityTypes = ['call', 'email', 'whatsapp', 'note', 'meeting'];
        foreach ($leads as $i => $lead) {
            Activity::create([
                'type' => $activityTypes[$i % count($activityTypes)],
                'subject' => 'Follow up ' . $lead->name,
                'description' => 'Sudah dihubungi dan tertarik dengan produk kami.',
                'lead_id' => $lead->id,
                'user_id' => $sales1->id,
                'activity_at' => now()->subDays(rand(0, 5)),
                'status' => 'completed',
                'outcome' => 'Positif',
            ]);
        }

        // Create tasks
        Task::create([
            'title' => 'Kirim proposal ke PT Maju Bersama',
            'description' => 'Siapkan proposal lengkap dengan detail harga',
            'lead_id' => $leads[2]->id,
            'assigned_to' => $manager->id,
            'created_by' => $admin->id,
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(2),
        ]);

        Task::create([
            'title' => 'Demo produk untuk Rizky Hidayat',
            'lead_id' => $leads[4]->id,
            'assigned_to' => $sales1->id,
            'created_by' => $admin->id,
            'priority' => 'urgent',
            'status' => 'in_progress',
            'due_date' => now()->addDay(),
        ]);

        Task::create([
            'title' => 'Follow up lead Maya Putri',
            'lead_id' => $leads[7]->id,
            'assigned_to' => $manager->id,
            'created_by' => $admin->id,
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => now()->subDay(), // overdue
        ]);

        // Create meetings
        Meeting::create([
            'title' => 'Demo Produk - Bintang Pratama',
            'description' => 'Presentasi solusi digital untuk PT Digital Nusantara',
            'start_at' => now()->addDays(2)->setTime(10, 0),
            'end_at' => now()->addDays(2)->setTime(11, 0),
            'location' => 'Kantor PT Digital Nusantara, Bandung',
            'lead_id' => $leads[2]->id,
            'created_by' => $manager->id,
            'status' => 'scheduled',
        ]);

        Meeting::create([
            'title' => 'Negosiasi Final - Rizky Hidayat',
            'description' => 'Diskusi harga akhir dan kontrak',
            'start_at' => now()->addDays(1)->setTime(14, 0),
            'end_at' => now()->addDays(1)->setTime(15, 30),
            'location' => 'Online via Zoom',
            'meeting_link' => 'https://zoom.us/j/123456789',
            'lead_id' => $leads[4]->id,
            'created_by' => $sales1->id,
            'status' => 'scheduled',
        ]);
    }
}
