<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Kuisioner',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ],
        );

        $this->seedActiveQuestionnaires($admin);
    }

    private function seedActiveQuestionnaires(User $admin): void
    {
        $questionnaires = [
            [
                'token' => 'fpsi-skala-kesejahteraan-mahasiswa',
                'title' => 'Skala Kesejahteraan Mahasiswa',
                'description' => 'Kuisioner untuk memetakan kondisi kesejahteraan psikologis mahasiswa Fakultas Psikologi UIN Suska Riau.',
                'expired_at' => now()->addMonths(2),
                'questions' => [
                    [
                        'text' => 'Saya merasa mampu menjalani aktivitas akademik dengan baik.',
                        'type' => Question::TYPE_RADIO,
                        'required' => true,
                        'options' => ['Sangat tidak setuju', 'Tidak setuju', 'Netral', 'Setuju', 'Sangat setuju'],
                    ],
                    [
                        'text' => 'Saya memiliki dukungan sosial yang cukup dari teman, keluarga, atau lingkungan kampus.',
                        'type' => Question::TYPE_RADIO,
                        'required' => true,
                        'options' => ['Sangat tidak setuju', 'Tidak setuju', 'Netral', 'Setuju', 'Sangat setuju'],
                    ],
                    [
                        'text' => 'Hal apa yang paling membantu Anda menjaga kesejahteraan psikologis?',
                        'type' => Question::TYPE_PARAGRAPH,
                        'required' => false,
                    ],
                ],
            ],
            [
                'token' => 'fpsi-survey-layanan-akademik',
                'title' => 'Survey Kepuasan Layanan Akademik',
                'description' => 'Survey untuk menghimpun masukan mahasiswa terkait layanan administrasi, informasi akademik, dan dukungan fakultas.',
                'expired_at' => now()->addMonth(),
                'questions' => [
                    [
                        'text' => 'Bagaimana penilaian Anda terhadap kecepatan layanan administrasi akademik?',
                        'type' => Question::TYPE_DROPDOWN,
                        'required' => true,
                        'options' => ['Sangat kurang', 'Kurang', 'Cukup', 'Baik', 'Sangat baik'],
                    ],
                    [
                        'text' => 'Media informasi akademik apa yang paling sering Anda gunakan?',
                        'type' => Question::TYPE_CHECKBOX,
                        'required' => true,
                        'options' => ['Website fakultas', 'WhatsApp', 'Instagram', 'Pengumuman kelas', 'Dosen/PA'],
                    ],
                    [
                        'text' => 'Saran untuk peningkatan layanan akademik fakultas.',
                        'type' => Question::TYPE_PARAGRAPH,
                        'required' => false,
                    ],
                ],
            ],
            [
                'token' => 'fpsi-minat-riset-psikologi',
                'title' => 'Asesmen Minat Riset Psikologi',
                'description' => 'Form untuk mengidentifikasi minat bidang riset mahasiswa sebagai bahan pengembangan laboratorium dan kolaborasi akademik.',
                'expired_at' => null,
                'questions' => [
                    [
                        'text' => 'Bidang riset psikologi yang paling Anda minati.',
                        'type' => Question::TYPE_DROPDOWN,
                        'required' => true,
                        'options' => ['Psikologi klinis', 'Psikologi pendidikan', 'Psikologi industri dan organisasi', 'Psikologi sosial', 'Psikologi perkembangan'],
                    ],
                    [
                        'text' => 'Apakah Anda berminat mengikuti kegiatan riset atau laboratorium fakultas?',
                        'type' => Question::TYPE_RADIO,
                        'required' => true,
                        'options' => ['Ya', 'Tidak', 'Masih mempertimbangkan'],
                    ],
                    [
                        'text' => 'Tuliskan topik riset yang ingin Anda kembangkan.',
                        'type' => Question::TYPE_SHORT_TEXT,
                        'required' => false,
                    ],
                ],
            ],
        ];

        foreach ($questionnaires as $questionnaireData) {
            $questionnaire = Questionnaire::updateOrCreate(
                ['public_token' => $questionnaireData['token']],
                [
                    'user_id' => $admin->id,
                    'title' => $questionnaireData['title'],
                    'description' => $questionnaireData['description'],
                    'is_active' => true,
                    'expired_at' => $questionnaireData['expired_at'],
                ],
            );

            $questionnaire->questions()->delete();

            foreach ($questionnaireData['questions'] as $index => $questionData) {
                $question = $questionnaire->questions()->create([
                    'question_text' => $questionData['text'],
                    'question_type' => $questionData['type'],
                    'is_required' => $questionData['required'],
                    'order_number' => $index + 1,
                ]);

                foreach ($questionData['options'] ?? [] as $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                    ]);
                }
            }
        }
    }
}
