<x-app-layout>
    <div class="changes">
        <h1>QRZ Log Viewer Change History</h1>
        <p style="margin-bottom: 1em">There have been <b>{{ $count }}</b> versioned releases since {{ $first }}.<br>
        To see details for any build, click the link provided.</p>
        <ul>
        {!! $changes !!}
        </ul>
    </div>
</x-app-layout>
