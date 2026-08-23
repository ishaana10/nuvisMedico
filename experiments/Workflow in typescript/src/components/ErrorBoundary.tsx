import React, { Component, ErrorInfo, ReactNode } from 'react';

interface Props {
  children: ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
}

export class ErrorBoundary extends Component<Props, State> {
  public state: State = {
    hasError: false,
    error: null,
  };

  public static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error };
  }

  public componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    console.error("ClinicFlow React ErrorBoundary caught an error:", error, errorInfo);
  }

  public render() {
    if (this.state.hasError) {
      return (
        <div className="p-8 max-w-2xl mx-auto my-12 bg-white border border-rose-300 rounded-xl shadow-sm text-xs font-mono space-y-4">
          <div className="flex items-center gap-2 text-rose-700 font-bold text-sm">
            <span>⚠ Application Error Encountered</span>
          </div>
          <p className="text-gray-700 font-sans">
            A rendering exception occurred in the application view. Please check browser console logs.
          </p>
          {this.state.error && (
            <div className="p-3 bg-rose-50 border border-rose-200 text-rose-900 rounded overflow-x-auto">
              {this.state.error.toString()}
            </div>
          )}
          <div className="flex gap-3 pt-2">
            <button
              onClick={() => window.location.reload()}
              className="px-4 py-2 bg-black text-white text-xs uppercase font-bold rounded cursor-pointer"
            >
              Reload Portal
            </button>
            <a
              href="?view=classic"
              className="px-4 py-2 bg-gray-100 text-gray-800 border border-gray-300 text-xs uppercase font-bold rounded cursor-pointer"
            >
              Switch to Classic Dashboard
            </a>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}
