@if ($announcements->isEmpty())
<div class="alert alert-info" role="alert">
    No announcement search results found.
</div>
@else
<div class="row">
    @foreach ($announcements as $announcement)
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" style="font-size: 20px;">
                        {{ $announcement->subject }}
                    </h5>
                    <h6 class="card-subtitle text-muted" style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}
                    </h6>
                    <p class="card-subtitle text-muted mt-2" style="font-size:12px;">
                        To:
                        @foreach ($announcement->displayEmails as $email)
                            {{ $email }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                        @if ($announcement->moreEmailsCount > 0)
                            and {{ $announcement->moreEmailsCount }} more
                        @endif
                    </p>
                </div>
                <div class="card-body">
                    <p class="card-text">{!! $announcement->message !!}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif