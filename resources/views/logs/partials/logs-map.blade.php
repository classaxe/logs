<table class="map map_layout" style="display: none">
    <tbody style="background: transparent">
    <tr>
        <td>
            <div class="scroll">
                <div id="scrollablelist" style="height: 473px;">
                    <table id="gsqs" class="results">
                        <thead>
                        <tr>
                            <th class="sort sorted" style="width: 3.25em" data-field="gsq">GSQ</th>
                            <th class="sort show_map_bands" data-field="bands_html"><a href="#" id="trigger_show_map_calls">Bands<br><i>(Click to show calls)</i></a></th>
                            <th class="sort show_map_calls" data-field="calls_html"><a href="#" id="trigger_show_map_bands">Calls<br><i>(Click to show bands)</i></a></th>
                            <th class="sort txt_vertical show_map_calls" data-field="bands_count"><div>Bands</div></th>
                            <th class="sort txt_vertical show_map_bands" data-field="calls_count"><div>Calls</div></th>
                            <th class="sort txt_vertical" data-field="logs_count"><div>Logs</div></th>
                            <th class="sort txt_vertical" data-field="conf"><div>Conf</div></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </td>
        <td class="map">
            <div id="header">
                <div class="form_layers">
                    <div>
                        <label>
                            <strong>Show</strong>
                        </label>
                    </div>
                    <div>
                        <label title="Show Maidenhead Locator Grid Squares">
                            <input type="checkbox" id="layer_grid" checked="checked">
                            Grid
                        </label>
                    </div>
                    <div>
                        <label title="Show Daytime / Nighttime">
                            <input type="checkbox" id="layer_night" checked="checked">
                            Night
                        </label>
                    </div>
                    <div>
                        <label title="Show Gridsquares">
                            <input type="checkbox" id="layer_squares" checked="checked">
                            Squares
                        </label>
                    </div>
                    <div>
                        <label title="Show QTH">
                            <input type="checkbox" id="layer_qth" checked="checked">
                            QTH
                        </label>
                    </div>
                </div>
            </div>
            <div id="map" style="height: 1000px;">Loading...</div>
        </td>
    </tr>
    </tbody>
</table>
