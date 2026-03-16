    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\DB;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::table('news', function (Blueprint $table) {
                $table->dateTime('published_at')->nullable()->after('content');
            });

            // Set existing records to use created_at as published_at
            DB::table('news')->update(['published_at' => DB::raw('created_at')]);
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('published_at');
            });
        }
    };
