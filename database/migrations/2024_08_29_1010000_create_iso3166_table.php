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
        Schema::create('iso3166', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('flag');
            $table->timestamps();
        });

        $imports = [
            ["Afghanistan", "af"],
            ["Albania", "al"],
            ["Algeria", "dz"],
            ["American Samoa", "as"],
            ["Andorra", "ad"],
            ["Angola", "ao"],
            ["Anguilla", "ai"],
            ["Antarctica", "aq"],
            ["Antigua and Barbuda", "ag"],
            ["Argentina", "ar"],
            ["Armenia", "am"],
            ["Aruba", "aw"],
            ["Australia", "au"],
            ["Austria", "at"],
            ["Azerbaijan", "az"],
            ["Bahamas (the)", "bs"],
            ["Bahrain", "bh"],
            ["Bangladesh", "bd"],
            ["Barbados", "bb"],
            ["Belarus", "by"],
            ["Belgium", "be"],
            ["Belize", "bz"],
            ["Benin", "bj"],
            ["Bermuda", "bm"],
            ["Bhutan", "bt"],
            ["Bolivia (Plurinational State of)", "bo"],
            ["Bonaire, Sint Eustatius and Saba", "bq"],
            ["Bosnia and Herzegovina", "ba"],
            ["Botswana", "bw"],
            ["Bouvet Island", "bv"],
            ["Brazil", "br"],
            ["British Indian Ocean Territory (the)", "io"],
            ["Brunei Darussalam", "bn"],
            ["Bulgaria", "bg"],
            ["Burkina Faso", "bf"],
            ["Burundi", "bi"],
            ["Cabo Verde", "cv"],
            ["Cambodia", "kh"],
            ["Cameroon", "cm"],
            ["Canada", "ca"],
            ["Cayman Islands (the)", "ky"],
            ["Central African Republic (the)", "cf"],
            ["Chad", "td"],
            ["Chile", "cl"],
            ["China", "cn"],
            ["Christmas Island", "cx"],
            ["Cocos (Keeling) Islands (the)", "cc"],
            ["Colombia", "co"],
            ["Comoros (the)", "km"],
            ["Congo (the Democratic Republic of the)", "cd"],
            ["Congo (the)", "cg"],
            ["Cook Islands (the)", "ck"],
            ["Costa Rica", "cr"],
            ["Croatia", "hr"],
            ["Cuba", "cu"],
            ["Curaçao", "cw"],
            ["Cyprus", "cy"],
            ["Czechia", "cz"],
            ["Côte d'Ivoire", "ci"],
            ["Denmark", "dk"],
            ["Djibouti", "dj"],
            ["Dominica", "dm"],
            ["Dominican Republic (the)", "do"],
            ["Ecuador", "ec"],
            ["Egypt", "eg"],
            ["El Salvador", "sv"],
            ["Equatorial Guinea", "gq"],
            ["Eritrea", "er"],
            ["Estonia", "ee"],
            ["Eswatini", "sz"],
            ["Ethiopia", "et"],
            ["Falkland Islands (the) [Malvinas]", "fk"],
            ["Faroe Islands (the)", "fo"],
            ["Fiji", "fj"],
            ["Finland", "fi"],
            ["France", "fr"],
            ["French Guiana", "gf"],
            ["French Polynesia", "pf"],
            ["French Southern Territories (the)", "tf"],
            ["Gabon", "ga"],
            ["Gambia (the)", "gm"],
            ["Georgia", "ge"],
            ["Germany", "de"],
            ["Ghana", "gh"],
            ["Gibraltar", "gi"],
            ["Greece", "gr"],
            ["Greenland", "gl"],
            ["Grenada", "gd"],
            ["Guadeloupe", "gp"],
            ["Guam", "gu"],
            ["Guatemala", "gt"],
            ["Guernsey", "gg"],
            ["Guinea", "gn"],
            ["Guinea-Bissau", "gw"],
            ["Guyana", "gy"],
            ["Haiti", "ht"],
            ["Heard Island and McDonald Islands", "hm"],
            ["Holy See (the)", "va"],
            ["Honduras", "hn"],
            ["Hong Kong", "hk"],
            ["Hungary", "hu"],
            ["Iceland", "is"],
            ["India", "in"],
            ["Indonesia", "id"],
            ["Iran (Islamic Republic of)", "ir"],
            ["Iraq", "iq"],
            ["Ireland", "ie"],
            ["Isle of Man", "im"],
            ["Israel", "il"],
            ["Italy", "it"],
            ["Jamaica", "jm"],
            ["Japan", "jp"],
            ["Jersey", "je"],
            ["Jordan", "jo"],
            ["Kazakhstan", "kz"],
            ["Kenya", "ke"],
            ["Kiribati", "ki"],
            ["Korea (the Democratic People's Republic of)", "kp"],
            ["Korea (the Republic of)", "kr"],
            ["Kuwait", "kw"],
            ["Kyrgyzstan", "kg"],
            ["Lao People's Democratic Republic (the)", "la"],
            ["Latvia", "lv"],
            ["Lebanon", "lb"],
            ["Lesotho", "ls"],
            ["Liberia", "lr"],
            ["Libya", "ly"],
            ["Liechtenstein", "li"],
            ["Lithuania", "lt"],
            ["Luxembourg", "lu"],
            ["Macao", "mo"],
            ["Madagascar", "mg"],
            ["Malawi", "mw"],
            ["Malaysia", "my"],
            ["Maldives", "mv"],
            ["Mali", "ml"],
            ["Malta", "mt"],
            ["Marshall Islands (the)", "mh"],
            ["Martinique", "mq"],
            ["Mauritania", "mr"],
            ["Mauritius", "mu"],
            ["Mayotte", "yt"],
            ["Mexico", "mx"],
            ["Micronesia (Federated States of)", "fm"],
            ["Moldova (the Republic of)", "md"],
            ["Monaco", "mc"],
            ["Mongolia", "mn"],
            ["Montenegro", "me"],
            ["Montserrat", "ms"],
            ["Morocco", "ma"],
            ["Mozambique", "mz"],
            ["Myanmar", "mm"],
            ["Namibia", "na"],
            ["Nauru", "nr"],
            ["Nepal", "np"],
            ["Netherlands (the)", "nl"],
            ["New Caledonia", "nc"],
            ["New Zealand", "nz"],
            ["Nicaragua", "ni"],
            ["Niger (the)", "ne"],
            ["Nigeria", "ng"],
            ["Niue", "nu"],
            ["Norfolk Island", "nf"],
            ["Northern Mariana Islands (the)", "mp"],
            ["Norway", "no"],
            ["Oman", "om"],
            ["Pakistan", "pk"],
            ["Palau", "pw"],
            ["Palestine, State of", "ps"],
            ["Panama", "pa"],
            ["Papua New Guinea", "pg"],
            ["Paraguay", "py"],
            ["Peru", "pe"],
            ["Philippines (the)", "ph"],
            ["Pitcairn", "pn"],
            ["Poland", "pl"],
            ["Portugal", "pt"],
            ["Puerto Rico", "pr"],
            ["Qatar", "qa"],
            ["Republic of North Macedonia", "mk"],
            ["Romania", "ro"],
            ["Russian Federation (the)", "ru"],
            ["Rwanda", "rw"],
            ["Réunion", "re"],
            ["Saint Barthélemy", "bl"],
            ["Saint Helena, Ascension and Tristan da Cunha", "sh"],
            ["Saint Kitts and Nevis", "kn"],
            ["Saint Lucia", "lc"],
            ["Saint Martin (French part)", "mf"],
            ["Saint Pierre and Miquelon", "pm"],
            ["Saint Vincent and the Grenadines", "vc"],
            ["Samoa", "ws"],
            ["San Marino", "sm"],
            ["Sao Tome and Principe", "st"],
            ["Saudi Arabia", "sa"],
            ["Senegal", "sn"],
            ["Serbia", "rs"],
            ["Seychelles", "sc"],
            ["Sierra Leone", "sl"],
            ["Singapore", "sg"],
            ["Sint Maarten (Dutch part)", "sx"],
            ["Slovakia", "sk"],
            ["Slovenia", "si"],
            ["Solomon Islands", "sb"],
            ["Somalia", "so"],
            ["South Africa", "za"],
            ["South Georgia and the South Sandwich Islands", "gs"],
            ["South Sudan", "ss"],
            ["Spain", "es"],
            ["Sri Lanka", "lk"],
            ["Sudan (the)", "sd"],
            ["Suriname", "sr"],
            ["Svalbard and Jan Mayen", "sj"],
            ["Sweden", "se"],
            ["Switzerland", "ch"],
            ["Syrian Arab Republic", "sy"],
            ["Taiwan (Province of China)", "tw"],
            ["Tajikistan", "tj"],
            ["Tanzania, United Republic of", "tz"],
            ["Thailand", "th"],
            ["Timor-Leste", "tl"],
            ["Togo", "tg"],
            ["Tokelau", "tk"],
            ["Tonga", "to"],
            ["Trinidad and Tobago", "tt"],
            ["Tunisia", "tn"],
            ["Turkey", "tr"],
            ["Turkmenistan", "tm"],
            ["Turks and Caicos Islands (the)", "tc"],
            ["Tuvalu", "tv"],
            ["Uganda", "ug"],
            ["Ukraine", "ua"],
            ["United Arab Emirates (the)", "ae"],
            ["United Kingdom of Great Britain and Northern Ireland (the)", "gb"],
            ["United States Minor Outlying Islands (the)", "um"],
            ["United States of America (the)", "us"],
            ["Uruguay", "uy"],
            ["Uzbekistan", "uz"],
            ["Vanuatu", "vu"],
            ["Venezuela (Bolivarian Republic of)", "ve"],
            ["Viet Nam", "vn"],
            ["Virgin Islands (British)", "vg"],
            ["Virgin Islands (U.S.)", "vi"],
            ["Wallis and Futuna", "wf"],
            ["Western Sahara", "eh"],
            ["Yemen", "ye"],
            ["Zambia", "zm"],
            ["Zimbabwe", "zw"],
            ["Åland Islands", "ax"]
        ];

        $data = [];
        foreach ($imports as $import) {
            $data[] = [
                'id' =>         null,
                'country' =>    $import[0],
                'flag' =>       $import[1],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('iso3166')->insert($data);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iso3166');
    }
};
