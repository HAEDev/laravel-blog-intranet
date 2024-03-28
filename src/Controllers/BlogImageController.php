<?php

namespace Lnch\LaravelBlog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lnch\LaravelBlog\Models\BlogImage;
use Illuminate\Http\UploadedFile;
use Lnch\LaravelBlog\Requests\BlogImageRequest;

class BlogImageController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (config("laravel-blog.use_auth_middleware", false)) {
            $this->middleware("auth");
        }

        if (!config("laravel-blog.images.enabled")) {
            abort(404);
        }
    }

    /**
     * Displays a full list of the active site's uploaded blog images
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if(auth()->user()->cannot("view", BlogImage::class)) {
            abort(403);
        }

        $images = $this->imageModel->paginate(config("laravel-blog.images.per_page"));

        return view($this->viewPath."images.index", [
            'images' => $images
        ]);
    }

    /**
     * Presents the interface to upload a new image or images
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if(auth()->user()->cannot("create", BlogImage::class)) {
            abort(403);
        }

        return view($this->viewPath."images.create");
    }

    /**
     * Handles upload of images and stores them in the database
     *
     * @param BlogImageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlogImageRequest $request)
    {
        if(auth()->user()->cannot("create", BlogImage::class)) {
            abort(403);
        }

        // Upload files, create records
        foreach($request->images as $image)
        {
            DB::transaction(function() use($image, $request) {
                $this->uploadFile($image, $request);
            });
        }

        $returnUrl = blogUrl("images");
        if($request->get("laravel-blog-embed", false) && $request->get("laravel-blog-featured", false)) {
            $returnUrl .= "?laravel-blog-embed=true&laravel-blog-featured=true";
        } else if ($request->get("laravel-blog-embed", false)) {
            $returnUrl .= "?laravel-blog-embed=true";
        } else if ($request->get("laravel-blog-featured", false)) {
            $returnUrl .= "?laravel-blog-featured=true";
        }

        // Return
        return redirect($returnUrl)
            ->with("success", (!$request->get("laravel-blog-embed", false)) ? "Images uploaded successfully!" : '');
    }

    /**
     * Displays a form for editing the selected BlogImage.
     *
     * @param BlogImage $image
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($image)
    {
        if(auth()->user()->cannot("edit", $image)) {
            abort(403);
        }

        return view($this->viewPath."images.edit", [
            'image' => $image
        ]);
    }

    /**
     * Updates the given resource
     *
     * @param BlogImageRequest $request
     * @param BlogImage        $image
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BlogImageRequest $request, $image)
    {
        if(auth()->user()->cannot("edit", $image)) {
            abort(403);
        }

        $image->update($request->only([
            "caption",
            "alt_text"
        ]));

        // Return
        return redirect($this->routePrefix."images")
            ->with("success", "Image updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BlogImage $image
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy($image)
    {
        if(auth()->user()->cannot("delete", $image)) {
            abort(403);
        }

        $image->delete();

        // Return
        return redirect($this->routePrefix."images")
            ->with("success", "Image deleted successfully!");
    }

    /**
     * Uploads a file to the server and creates a DB entry.
     *
     * @param UploadedFile     $file
     * @param BlogImageRequest $request
     * @return string
     * @throws \Exception
     */
    private function uploadFile(UploadedFile $file, $request)
    {
        // Create filename
        $originalFilename = $file->getClientOriginalName();

        $patterns = [
            '@\[date\]@is',
            '@\[datetime\]@is',
            '@\[filename\]@is',
        ];

        $matches = [
            date("Ymd"),
            date("Ymd-His"),
            str_replace(" ", "_", $originalFilename),
        ];

        $filenamePattern = config("laravel-blog.images.filename_format", "[datetime]_[filename]");
        $filename = preg_replace($patterns, $matches, $filenamePattern);

        $caption = $request->caption ? $request->caption[$originalFilename] : '';
        $alt_text = $request->alt_text ? $request->alt_text[$originalFilename] : '';

        $storageLocation = config("laravel-blog.images.storage_location");

        // Create DB record
        $this->imageModel->create([
            'site_id' => getBlogSiteID(),
            'storage_location' => $storageLocation,
            'path' => $filename,
            'caption' => $caption,
            'alt_text' => $alt_text,
        ]);

        // Upload file
        if ($storageLocation == "public")
        {
            $destinationPath = public_path(config("laravel-blog.images.storage_path"));
        }
        else if($storageLocation == "storage")
        {
            $destinationPath = storage_path("app/public/".config("laravel-blog.images.storage_path"));
        }
        else
        {
            throw new \Exception("images.storage_path has not been properly defined");
        }

        $file->move($destinationPath, $filename);

        return $filename;
    }

    /**
     * Retrieves a request to upload an image from the CKEditor
     *
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function dialogUpload(Request $request)
    {
        $files = request()->file('upload');
        $error_bag = [];
        foreach (is_array($files) ? $files : [$files] as $file)
        {
            $new_filename = $this->uploadFile($file, $request);
        }

        $response = $this->useFile($new_filename);

        return $response;
    }

    /**
     * Automatically populates the URL field on CKEditor after
     * a successful upload.
     *
     * @param $new_filename
     * @return string
     * @throws \Exception
     */
    private function useFile($new_filename)
    {
        if(config("laravel-blog.images.storage_location") == "storage")
        {
            $file = url("storage/".config("laravel-blog.images.storage_path")."/".$new_filename);
        }
        else if(config("laravel-blog.images.storage_location") == "public")
        {
            $file = url(config("laravel-blog.images.storage_path")."/".$new_filename);
        }
        else
        {
            throw new \Exception("images.storage_path has not been properly defined");
        }

        return "<script type='text/javascript'>

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        }

        var funcNum = getUrlParam('CKEditorFuncNum');

        var par = window.parent,
            op = window.opener,
            o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);

        if (op) window.close();
        if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '$file');
        </script>";
    }
}
