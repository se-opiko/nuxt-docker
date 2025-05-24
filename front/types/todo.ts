export type Task = {
  id: number;
  title: string;
  description: string;
  priority: number;
  status: number;
  updated_at: string;
  created_at: string;
}

export type RuleForm = {
  title: string;
  description: string;
  priority: number;
  status: number;
}

export type ApiResponse = {
  tasks: Task[];
}