<?php

namespace App\Http\Controllers;

class ChangesController extends Controller
{
    const REPO_BASE = "https://github.com/classaxe/logs";
    const NEW_DAYS = 3;
    public static function index()
    {
        $tweaks = [
            [
                'Maksym Shyskov',
                '[[',
                ']]',
                '[]',
                '[',
                ']'
            ],
            [
                '[Maksym]',
                '[',
                ']',
                '[]',
                '<span>',
                '</span>'
            ]
        ];


        $changelog = explode("\n", `git log main --pretty=format:"%ad %H %s" --date=short`);
        $commits = [];
        $first = null;
        foreach ($changelog as &$entry) {
            $bits =     explode(' ', $entry);
            $date =     array_shift($bits);
            if ($first === null) {
                $first = $date;
            }
            $new =      round(
                    $datediff = (time() - strtotime($date)) / (60 * 60 * 24)
                ) <= static::NEW_DAYS;
            $hash =     trim(array_shift($bits), ':');
            $version =  trim(array_shift($bits), ':');
            $details =  htmlentities(implode(' ', $bits));
            $commits[] =
                '<li id="' . $version .'">'
                . '<a href="' . static::REPO_BASE . '/commit/' . $hash .'" target="_blank"><strong>'.$version.'</strong></a> '
                . ' <em>('.$date.')</em> '
                .($new ? '<span class="new">NEW</span> ' : '')
                . '<br />'
                . $details
                . '</li>';
        }
        $changes = str_replace($tweaks[0], $tweaks[1], implode("\n", $commits));
        $changes = str_replace('<span></span>', '[]', $changes);

        return view('changes', [
            'changes' =>    $changes,
            'count' =>      count($commits),
            'first' =>      $first
        ]);
    }
}
