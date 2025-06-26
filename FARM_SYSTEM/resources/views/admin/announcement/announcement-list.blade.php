@if ($announcements->isEmpty())
    <div class="alert alert-info text-center" role="alert">
        No announcement search results found.
    </div>
@else
    @foreach ($announcements as $announcement)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $announcement->subject }}</h5>
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}
                    </small>
                    <div class="mt-2">
                        To: @foreach ($announcement->displayEmails as $email)
                            {{ $email }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                        @if ($announcement->moreEmailsCount > 0)
                            and {{ $announcement->moreEmailsCount }} more
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @if ($announcement->published)
                        <span class="badge badge-success mr-3">Published</span>
                    @else
                        <span class="badge badge-warning mr-3">Unpublished</span>
                    @endif
                    <div class="dropdown">
                        <i class="fas fa-ellipsis-h" id="dropdownMenuButton{{ $announcement->id_announcement }}"
                            data-bs-toggle="dropdown" aria-expanded="false"></i>
                        <ul class="dropdown-menu"
                            aria-labelledby="dropdownMenuButton{{ $announcement->id_announcement }}">
                            <li><a class="dropdown-item"
                                    href="{{ route('admin.announcement.edit-announcement', $announcement->id_announcement) }}">Edit</a>
                            </li>
                            <li><button type="button" class="dropdown-item delete-btn"
                                    data-id="{{ $announcement->id_announcement }}">Delete</button></li>
                            @if ($announcement->published)
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.announcement.unpublish-announcement', $announcement->id_announcement) }}">Unpublish</a>
                                </li>
                            @else
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.announcement.publish-announcement', $announcement->id_announcement) }}">Publish</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text">{!! $announcement->message !!}</p>
            </div>
        </div>
    @endforeach
@endif
