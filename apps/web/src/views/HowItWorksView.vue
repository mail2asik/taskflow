<template>
  <div class="py-16 max-w-5xl mx-auto px-4 space-y-16">
    <div class="text-center space-y-4">
      <h1 class="text-4xl font-extrabold text-slate-900">How TaskFlow Works</h1>
      <p class="text-slate-600">Four simple steps to organized product development.</p>
    </div>

    <div class="space-y-12">
      <!-- Step 1 -->
      <div class="flex flex-col md:flex-row items-center gap-8 bg-white p-8 rounded-2xl border border-slate-200">
        <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-black text-2xl flex items-center justify-center shrink-0">
          01
        </div>
        <div class="space-y-2">
          <h3 class="text-xl font-bold text-slate-900">Create Your Project</h3>
          <p class="text-slate-600 text-sm leading-relaxed">
            Set up project repositories, define unique keys (e.g., OP, PROJ), and configure labels and role permissions.
          </p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="flex flex-col md:flex-row items-center gap-8 bg-white p-8 rounded-2xl border border-slate-200">
        <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-black text-2xl flex items-center justify-center shrink-0">
          02
        </div>
        <div class="space-y-2">
          <h3 class="text-xl font-bold text-slate-900">Add Issues and Tasks</h3>
          <p class="text-slate-600 text-sm leading-relaxed">
            Create issues with title, description, priority, assignee, and tags. Auto-increment keys like `OP-101` keep tasks traceable.
          </p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="flex flex-col md:flex-row items-center gap-8 bg-white p-8 rounded-2xl border border-slate-200">
        <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-black text-2xl flex items-center justify-center shrink-0">
          03
        </div>
        <div class="space-y-2">
          <h3 class="text-xl font-bold text-slate-900">Manage via Kanban</h3>
          <p class="text-slate-600 text-sm leading-relaxed">
            Drag tasks through customizable swimlanes (To Do, In Progress, In Review, Done) to keep sprint goals clear.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>