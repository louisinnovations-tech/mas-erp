import { useState, useRef } from 'react';
import { Button } from '@/components/ui/button';
import { UploadCloud, X } from 'lucide-react';

export function FileUploader({ accept = '*', multiple = false, onUpload }) {
  const [files, setFiles] = useState([]);
  const inputRef = useRef(null);

  const handleFileChange = (e) => {
    const selectedFiles = Array.from(e.target.files);
    setFiles(prev => [...prev, ...selectedFiles]);
    if (onUpload) {
      onUpload(selectedFiles);
    }
  };

  const removeFile = (index) => {
    setFiles(files.filter((_, i) => i !== index));
  };

  return (
    <div className="space-y-4">
      <div
        className="border-2 border-dashed rounded-lg p-8 text-center hover:border-primary/50 transition-colors"
        onDragOver={(e) => e.preventDefault()}
        onDrop={(e) => {
          e.preventDefault();
          const droppedFiles = Array.from(e.dataTransfer.files);
          setFiles(prev => [...prev, ...droppedFiles]);
          if (onUpload) {
            onUpload(droppedFiles);
          }
        }}
      >
        <input
          type="file"
          ref={inputRef}
          className="hidden"
          accept={accept}
          multiple={multiple}
          onChange={handleFileChange}
        />
        <UploadCloud className="mx-auto h-12 w-12 text-muted-foreground" />
        <div className="mt-4">
          <Button
            type="button"
            variant="outline"
            onClick={() => inputRef.current?.click()}
          >
            Choose Files
          </Button>
        </div>
        <p className="mt-2 text-sm text-muted-foreground">
          or drag and drop your files here
        </p>
      </div>

      {files.length > 0 && (
        <div className="space-y-2">
          {files.map((file, index) => (
            <div
              key={index}
              className="flex items-center justify-between p-2 border rounded"
            >
              <span className="text-sm truncate">{file.name}</span>
              <Button
                type="button"
                variant="ghost"
                size="sm"
                onClick={() => removeFile(index)}
              >
                <X className="h-4 w-4" />
              </Button>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}