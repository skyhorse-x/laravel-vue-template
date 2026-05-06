import axios from 'axios'

const requests = []
let requestId = 0

const interceptor = axios.create({
  baseURL: '/api',
  timeout: 10000
})

interceptor.interceptors.request.use((config) => {
  const id = ++requestId
  const request = {
    id,
    method: config.method?.toUpperCase() || 'GET',
    url: config.url,
    path: config.url,
    params: config.params || {},
    data: config.data || {},
    headers: { ...config.headers },
    timestamp: Date.now(),
    status: 'pending'
  }
  requests.unshift(request)
  config._requestId = id
  return config
})

interceptor.interceptors.response.use(
  (response) => {
    const request = requests.find(r => r.id === response.config._requestId)
    if (request) {
      request.status = 'success'
      request.response = {
        status: response.status,
        data: response.data,
        headers: response.headers
      }
      request.duration = Date.now() - request.timestamp
    }
    return response.data
  },
  (error) => {
    const request = requests.find(r => r.id === error.config?._requestId)
    if (request) {
      request.status = 'error'
      request.response = {
        status: error.response?.status,
        data: error.response?.data,
        headers: error.response?.headers
      }
      request.error = error.message
      request.duration = Date.now() - request.timestamp
    }
    return Promise.reject(error)
  }
)

export function getRequests() {
  return requests
}

export function clearRequests() {
  requests.length = 0
  requestId = 0
}

export function getRequestById(id) {
  return requests.find(r => r.id === id)
}

export function generateApiDocs() {
  const docs = []
  const groupMap = {}

  for (const req of requests) {
    if (req.status === 'pending') continue

    const path = req.path
    const method = req.method
    const key = `${method}:${path}`

    if (!groupMap[key]) {
      groupMap[key] = {
        method,
        path,
        description: getDescription(path, method),
        parameters: extractParams(req),
        requestBody: req.data && Object.keys(req.data).length > 0 ? req.data : null,
        responses: req.response ? {
          [req.response.status]: {
            description: getStatusDescription(req.response.status),
            example: req.response.data
          }
        } : null
      }
    }
  }

  for (const key in groupMap) {
    docs.push(groupMap[key])
  }

  docs.sort((a, b) => a.path.localeCompare(b.path))

  return docs
}

function getDescription(path, method) {
  const descriptions = {
    '/auth/register': '用户注册',
    '/auth/login': '用户登录',
    '/auth/logout': '用户登出',
    '/auth/me': '获取当前用户信息',
    '/auth/profile': '更新用户资料',
    '/auth/change-password': '修改密码',
    '/admin/login': '管理员登录',
    '/admin/logout': '管理员登出',
    '/admin/me': '获取管理员信息',
    '/admin/dashboard': '获取仪表盘数据',
    '/admin/profile': '更新管理员资料',
    '/admin/users': '用户管理',
    '/admin/admins': '管理员管理',
    '/admin/roles': '角色管理',
    '/admin/menus': '菜单管理',
    '/admin/articles': '文章管理',
    '/admin/products': '产品管理',
    '/admin/financial': '财务管理',
    '/admin/settings': '系统设置',
    '/admin/notifications': '通知管理',
    '/admin/operation-logs': '操作日志',
    '/admin/ip-blacklist': 'IP黑名单',
    '/front/articles': '前端文章列表',
    '/front/products': '前端产品列表',
    '/financial': '财务记录',
    '/user/notifications': '用户通知'
  }

  for (const pattern in descriptions) {
    if (path.includes(pattern) || path.endsWith(pattern)) {
      return descriptions[pattern]
    }
  }

  return `${method} ${path}`
}

function extractParams(req) {
  const params = []

  if (req.params && Object.keys(req.params).length > 0) {
    for (const key in req.params) {
      params.push({
        name: key,
        in: 'query',
        type: typeof req.params[key],
        description: `Query parameter: ${key}`
      })
    }
  }

  if (req.data && typeof req.data === 'object' && !Array.isArray(req.data)) {
    for (const key in req.data) {
      params.push({
        name: key,
        in: 'body',
        type: typeof req.data[key],
        description: `Request body: ${key}`,
        value: req.data[key]
      })
    }
  }

  return params
}

function getStatusDescription(status) {
  const descriptions = {
    200: 'OK',
    201: 'Created',
    204: 'No Content',
    400: 'Bad Request',
    401: 'Unauthorized',
    403: 'Forbidden',
    404: 'Not Found',
    422: 'Validation Error',
    500: 'Internal Server Error'
  }
  return descriptions[status] || 'Unknown'
}

export function exportApiDocs(format = 'json') {
  const docs = generateApiDocs()

  if (format === 'json') {
    return JSON.stringify(docs, null, 2)
  }

  if (format === 'markdown') {
    return generateMarkdownDocs(docs)
  }

  return JSON.stringify(docs, null, 2)
}

function generateMarkdownDocs(docs) {
  let md = '# API Documentation\n\n'
  md += `Generated at: ${new Date().toLocaleString()}\n\n`
  md += '## Table of Contents\n\n'

  const grouped = {}
  for (const doc of docs) {
    const group = getApiGroup(doc.path)
    if (!grouped[group]) grouped[group] = []
    grouped[group].push(doc)
  }

  for (const group in grouped) {
    md += `- [${group}](#${group.toLowerCase().replace(/\s+/g, '-')})\n`
  }

  for (const group in grouped) {
    md += `\n## ${group}\n\n`

    for (const doc of grouped[group]) {
      md += `### ${doc.method} ${doc.path}\n\n`
      md += `**Description:** ${doc.description}\n\n`

      if (doc.parameters && doc.parameters.length > 0) {
        md += '**Parameters:**\n\n'
        md += '| Name | In | Type | Description |\n'
        md += '|------|-----|------|-------------|\n'
        for (const p of doc.parameters) {
          md += `| ${p.name} | ${p.in} | ${p.type} | ${p.description} |\n`
        }
        md += '\n'
      }

      if (doc.requestBody) {
        md += '**Request Body:**\n\n'
        md += '```json\n'
        md += JSON.stringify(doc.requestBody, null, 2) + '\n'
        md += '```\n\n'
      }

      if (doc.responses) {
        md += '**Responses:**\n\n'
        for (const status in doc.responses) {
          md += `\`${status} ${doc.responses[status].description}\`\n\n`
          if (doc.responses[status].example) {
            md += '```json\n'
            md += JSON.stringify(doc.responses[status].example, null, 2) + '\n'
            md += '```\n\n'
          }
        }
      }

      md += '---\n\n'
    }
  }

  return md
}

function getApiGroup(path) {
  if (path.startsWith('/admin/auth') || path.startsWith('/auth')) return 'Authentication'
  if (path.startsWith('/admin/users') || path.startsWith('/user')) return 'User Management'
  if (path.startsWith('/admin/admins')) return 'Admin Management'
  if (path.startsWith('/admin/roles')) return 'Role Management'
  if (path.startsWith('/admin/menus')) return 'Menu Management'
  if (path.startsWith('/admin/articles')) return 'Article Management'
  if (path.startsWith('/admin/products')) return 'Product Management'
  if (path.startsWith('/admin/financial')) return 'Financial Management'
  if (path.startsWith('/admin/settings')) return 'Settings'
  if (path.startsWith('/admin/notifications')) return 'Notifications'
  if (path.startsWith('/admin/operation-logs')) return 'Operation Logs'
  if (path.startsWith('/admin/ip-blacklist')) return 'IP Blacklist'
  if (path.startsWith('/front')) return 'Frontend API'
  if (path.startsWith('/financial') || path.startsWith('/referral')) return 'Financial & Referral'
  return 'Other'
}

export default interceptor
