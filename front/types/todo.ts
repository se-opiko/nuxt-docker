/**
 * プロジェクトの型定義
 */
export type Project = {
  id: number;
  name: string;
  description: string | null;
  color: string | null;
  created_at: string;
  updated_at: string;
  tasks?: Task[];
}

/**
 * タスクの型定義
 */
export type Task = {
  id: number;
  title: string;
  description: string;
  priority: number;
  status: number;
  project_id: number | null;
  updated_at: string;
  created_at: string;
  project?: Project;
}

/**
 * タスクのフォーム用型定義
 */
export type RuleForm = {
  title: string;
  description: string;
  priority: number;
  status: number;
  project_id?: number | undefined;
}

/**
 * API レスポンスの型定義
 */
export type ApiResponse = {
  message: string;
  tasks: Task[];
}

/**
 * プロジェクトAPI レスポンスの型定義
 */
export type ProjectApiResponse = {
  message: string;
  projects: Project[];
}

/**
 * プロジェクトのフォーム用型定義
 */
export type ProjectForm = {
  name: string;
  description: string;
  color: string | null;
}