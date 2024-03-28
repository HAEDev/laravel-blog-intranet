@extends(config("laravel-blog.view_layout", "laravel-blog::layout"))

@section(config("laravel-blog.view_content", "content"))

    <div class="row">
        <div class="col-md-9">

            <article class="single-post">

                <figure class="post-image">
                    @if($post->featuredImage && $post->show_featured)
                        <img src="{{ $post->featuredImage->getUrl() }}" alt="{{ $post->featuredImage->alt_text }}" />
                    @endif
                </figure> <!-- End .post-image -->

                <div class="post-details">

                    <header>
                        <a href="{{ $post->url }}">
                            <h1 class="post-title">
                                {{ $post->title }}
                            </h1>
                        </a>

                        <div class="post-meta">
                            Posted on {{ $post->published_at->format("jS F, Y") }} at {{ $post->published_at->format("H:i") }}
                        </div>
                    </header>

                </div> <!-- End .post-details -->

                <div class="post-content">
                    {!! $post->content !!}
                </div>

                @if(count($post->files))
                <div class="files">
                    <h6>Attached Files</h6>
                    <table class="table-striped">
                        <tbody>
                            @foreach($post->files as $file)
                            <tr>
                                <td><a href="{{ $file->getUrl() }}" target="_blank">{{ $file->path }}</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

            </article>

            @if(config("laravel-blog.comments.enabled"))
                <span id="post-comments"></span>
                <h2>Comments</h2>
                @include("laravel-blog::frontend.partials.comments", ['comments' => $post->comments])
                @include("laravel-blog::frontend.partials.comments-form", ['post' => $post])
            @endif

        </div>
    </div>

@endsection
