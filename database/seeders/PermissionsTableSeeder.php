<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'service_access',
            ],
            [
                'id'    => 18,
                'title' => 'financial_assistance_create',
            ],
            [
                'id'    => 19,
                'title' => 'financial_assistance_edit',
            ],
            [
                'id'    => 20,
                'title' => 'financial_assistance_show',
            ],
            [
                'id'    => 21,
                'title' => 'financial_assistance_delete',
            ],
            [
                'id'    => 22,
                'title' => 'financial_assistance_access',
            ],
            [
                'id'    => 23,
                'title' => 'guarantee_letter_create',
            ],
            [
                'id'    => 24,
                'title' => 'guarantee_letter_edit',
            ],
            [
                'id'    => 25,
                'title' => 'guarantee_letter_show',
            ],
            [
                'id'    => 26,
                'title' => 'guarantee_letter_delete',
            ],
            [
                'id'    => 27,
                'title' => 'guarantee_letter_access',
            ],
            [
                'id'    => 28,
                'title' => 'burial_assistance_create',
            ],
            [
                'id'    => 29,
                'title' => 'burial_assistance_edit',
            ],
            [
                'id'    => 30,
                'title' => 'burial_assistance_show',
            ],
            [
                'id'    => 31,
                'title' => 'burial_assistance_delete',
            ],
            [
                'id'    => 32,
                'title' => 'burial_assistance_access',
            ],
            [
                'id'    => 33,
                'title' => 'medical_assistance_create',
            ],
            [
                'id'    => 34,
                'title' => 'medical_assistance_edit',
            ],
            [
                'id'    => 35,
                'title' => 'medical_assistance_show',
            ],
            [
                'id'    => 36,
                'title' => 'medical_assistance_delete',
            ],
            [
                'id'    => 37,
                'title' => 'medical_assistance_access',
            ],
            [
                'id'    => 38,
                'title' => 'solicitation_create',
            ],
            [
                'id'    => 39,
                'title' => 'solicitation_edit',
            ],
            [
                'id'    => 40,
                'title' => 'solicitation_show',
            ],
            [
                'id'    => 41,
                'title' => 'solicitation_delete',
            ],
            [
                'id'    => 42,
                'title' => 'solicitation_access',
            ],
            [
                'id'    => 43,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 44,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 45,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 46,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 47,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 48,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 49,
                'title' => 'contact_management_access',
            ],
            [
                'id'    => 50,
                'title' => 'contact_company_create',
            ],
            [
                'id'    => 51,
                'title' => 'contact_company_edit',
            ],
            [
                'id'    => 52,
                'title' => 'contact_company_show',
            ],
            [
                'id'    => 53,
                'title' => 'contact_company_delete',
            ],
            [
                'id'    => 54,
                'title' => 'contact_company_access',
            ],
            [
                'id'    => 55,
                'title' => 'contact_contact_create',
            ],
            [
                'id'    => 56,
                'title' => 'contact_contact_edit',
            ],
            [
                'id'    => 57,
                'title' => 'contact_contact_show',
            ],
            [
                'id'    => 58,
                'title' => 'contact_contact_delete',
            ],
            [
                'id'    => 59,
                'title' => 'contact_contact_access',
            ],
            [
                'id'    => 60,
                'title' => 'task_management_access',
            ],
            [
                'id'    => 61,
                'title' => 'task_status_create',
            ],
            [
                'id'    => 62,
                'title' => 'task_status_edit',
            ],
            [
                'id'    => 63,
                'title' => 'task_status_show',
            ],
            [
                'id'    => 64,
                'title' => 'task_status_delete',
            ],
            [
                'id'    => 65,
                'title' => 'task_status_access',
            ],
            [
                'id'    => 66,
                'title' => 'task_tag_create',
            ],
            [
                'id'    => 67,
                'title' => 'task_tag_edit',
            ],
            [
                'id'    => 68,
                'title' => 'task_tag_show',
            ],
            [
                'id'    => 69,
                'title' => 'task_tag_delete',
            ],
            [
                'id'    => 70,
                'title' => 'task_tag_access',
            ],
            [
                'id'    => 71,
                'title' => 'task_create',
            ],
            [
                'id'    => 72,
                'title' => 'task_edit',
            ],
            [
                'id'    => 73,
                'title' => 'task_show',
            ],
            [
                'id'    => 74,
                'title' => 'task_delete',
            ],
            [
                'id'    => 75,
                'title' => 'task_access',
            ],
            [
                'id'    => 76,
                'title' => 'tasks_calendar_access',
            ],
            [
                'id'    => 77,
                'title' => 'setting_access',
            ],
            [
                'id'    => 78,
                'title' => 'ngo_create',
            ],
            [
                'id'    => 79,
                'title' => 'ngo_edit',
            ],
            [
                'id'    => 80,
                'title' => 'ngo_show',
            ],
            [
                'id'    => 81,
                'title' => 'ngo_delete',
            ],
            [
                'id'    => 82,
                'title' => 'ngo_access',
            ],
            [
                'id'    => 83,
                'title' => 'sector_group_create',
            ],
            [
                'id'    => 84,
                'title' => 'sector_group_edit',
            ],
            [
                'id'    => 85,
                'title' => 'sector_group_show',
            ],
            [
                'id'    => 86,
                'title' => 'sector_group_delete',
            ],
            [
                'id'    => 87,
                'title' => 'sector_group_access',
            ],
            [
                'id'    => 88,
                'title' => 'barangay_create',
            ],
            [
                'id'    => 89,
                'title' => 'barangay_edit',
            ],
            [
                'id'    => 90,
                'title' => 'barangay_show',
            ],
            [
                'id'    => 91,
                'title' => 'barangay_delete',
            ],
            [
                'id'    => 92,
                'title' => 'barangay_access',
            ],
            [
                'id'    => 93,
                'title' => 'directory_create',
            ],
            [
                'id'    => 94,
                'title' => 'directory_edit',
            ],
            [
                'id'    => 95,
                'title' => 'directory_show',
            ],
            [
                'id'    => 96,
                'title' => 'directory_delete',
            ],
            [
                'id'    => 97,
                'title' => 'directory_access',
            ],
            [
                'id'    => 98,
                'title' => 'familycomposition_create',
            ],
            [
                'id'    => 99,
                'title' => 'familycomposition_edit',
            ],
            [
                'id'    => 100,
                'title' => 'familycomposition_show',
            ],
            [
                'id'    => 101,
                'title' => 'familycomposition_delete',
            ],
            [
                'id'    => 102,
                'title' => 'familycomposition_access',
            ],
            [
                'id'    => 103,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
