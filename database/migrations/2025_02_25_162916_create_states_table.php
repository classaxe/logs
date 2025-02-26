<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('sp');
            $table->string('name');
            $table->string('country');
            $table->timestamps();
        });

        $imports = [
            ["AB", "Alberta", "Canada"],
            ["BC", "British Columbia", "Canada"],
            ["MB", "Manitoba", "Canada"],
            ["NB", "New Brunswick", "Canada"],
            ["NL", "Newfd. & Lab.", "Canada"],
            ["NS", "Nova Scotia", "Canada"],
            ["NT", "Northwest Territories", "Canada"],
            ["NU", "Nunavut", "Canada"],
            ["ON", "Ontario", "Canada"],
            ["PE", "P.E.I.", "Canada"],
            ["QC", "Quebec", "Canada"],
            ["SK", "Saskatchewan", "Canada"],
            ["YT", "Yukon Territories", "Canada"],
            ["AL", "Alabama", "USA"],
            ["AK", "Alaska", "Alaska"],
            ["AZ", "Arizona", "USA"],
            ["AR", "Arkansas", "USA"],
            ["CA", "California", "USA"],
            ["CO", "Colorado", "USA"],
            ["CT", "Connecticut", "USA"],
            ["DE", "Delaware", "USA"],
            ["FL", "Florida", "USA"],
            ["GA", "Georgia", "USA"],
            ["HI", "Hawaii", "Hawaii"],
            ["ID", "Idaho", "USA"],
            ["IL", "Illinois", "USA"],
            ["IN", "Indiana", "USA"],
            ["IA", "Iowa", "USA"],
            ["KS", "Kansas", "USA"],
            ["KY", "Kentucky", "USA"],
            ["LA", "Louisiana", "USA"],
            ["ME", "Maine", "USA"],
            ["MD", "Maryland", "USA"],
            ["MA", "Massachusetts", "USA"],
            ["MI", "Michigan", "USA"],
            ["MN", "Minnesota", "USA"],
            ["MS", "Mississippi", "USA"],
            ["MO", "Missouri", "USA"],
            ["MT", "Montana", "USA"],
            ["NE", "Nebraska", "USA"],
            ["NV", "Nevada", "USA"],
            ["NH", "New Hampshire", "USA"],
            ["NJ", "New Jersey", "USA"],
            ["NM", "New Mexico", "USA"],
            ["NY", "New York", "USA"],
            ["NC", "North Carolina", "USA"],
            ["ND", "North Dakota", "USA"],
            ["OH", "Ohio", "USA"],
            ["OK", "Oklahoma", "USA"],
            ["OR", "Oregon", "USA"],
            ["PA", "Pennsylvania", "USA"],
            ["PR", "Puerto Rica", "Puerto Rico"],
            ["RI", "Rhode Island", "USA"],
            ["SC", "South Carolina", "USA"],
            ["SD", "South Dakota", "USA"],
            ["TN", "Tennessee", "USA"],
            ["TX", "Texas", "USA"],
            ["UT", "Utah", "USA"],
            ["VT", "Vermont", "USA"],
            ["VA", "Virginia", "USA"],
            ["WA", "Washington", "USA"],
            ["DC", "Washington D.C.", "USA"],
            ["WV", "West Virginia", "USA"],
            ["WI", "Wisconsin", "USA"],
            ["WY", "Wyoming", "USA"],
            ["VI", "Virgin Islands", "US Virgin Islands"],
            ["AT", "Australian Capital Territory", "Australia"],
            ["NW", "New South Wales", "Australia"],
            ["NN", "Northern Territory", "Australia"],
            ["QD", "Queensland", "Australia"],
            ["SA", "South Australia", "Australia"],
            ["TA", "Tasmania", "Australia"],
            ["VI", "Victoria", "Australia"],
            ["WE", "Western Australia", "Australia"]
        ];

        $data = [];
        foreach ($imports as $import) {
            $data[] = [
                'id' =>         null,
                'sp' =>         $import[0],
                'name' =>       $import[1],
                'country' =>    $import[2],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('states')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
};
