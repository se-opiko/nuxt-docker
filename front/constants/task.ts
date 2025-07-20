/**
 * ステータスの定数定義
 */
export const TASK_STATUS = {
  NOT_STARTED: 1,
  IN_PROGRESS: 2,
  COMPLETED: 3
} as const

/**
 * 優先度の定数定義
 */
export const TASK_PRIORITY = {
  LOW: 1,
  MEDIUM: 2,
  HIGH: 3
} as const

/**
 * ステータスの表示ラベル
 */
export const TASK_STATUS_LABELS = {
  [TASK_STATUS.NOT_STARTED]: '未着手',
  [TASK_STATUS.IN_PROGRESS]: '進行中',
  [TASK_STATUS.COMPLETED]: '完了'
} as const

/**
 * 優先度の表示ラベル
 */
export const TASK_PRIORITY_LABELS = {
  [TASK_PRIORITY.LOW]: '低',
  [TASK_PRIORITY.MEDIUM]: '中',
  [TASK_PRIORITY.HIGH]: '高'
} as const

/**
 * ステータスのタイプ定義（Element Plusのタグ色指定用）
 */
export const TASK_STATUS_TYPES = {
  [TASK_STATUS.NOT_STARTED]: 'info',
  [TASK_STATUS.IN_PROGRESS]: 'warning',
  [TASK_STATUS.COMPLETED]: 'success'
} as const

/**
 * 優先度のタイプ定義（Element Plusのタグ色指定用）
 */
export const TASK_PRIORITY_TYPES = {
  [TASK_PRIORITY.LOW]: 'success',
  [TASK_PRIORITY.MEDIUM]: 'warning',
  [TASK_PRIORITY.HIGH]: 'danger'
} as const 