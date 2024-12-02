import * as React from "react";
import { cn } from "@/lib/utils";

const Textarea = React.forwardRef(({ className, ...props }, ref) => {
  return (
    <textarea
      className={cn(
        "flex min-h-[60px] w-full rounded-md border border-accent bg-white px-3 py-2 text-sm text-[#576071] shadow-sm placeholder:text-[#576071]/50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-[#800000] disabled:cursor-not-allowed disabled:opacity-50",
        className
      )}
      ref={ref}
      {...props}
    />
  );
});
Textarea.displayName = "Textarea";

export { Textarea };